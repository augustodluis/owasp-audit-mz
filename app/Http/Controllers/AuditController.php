<?php

namespace App\Http\Controllers;

use App\Mail\AuditReportMail;
use App\Models\Audit;
use App\Services\CrawlerService;
use App\Services\NotificationService;
use App\Services\ReportService;
use App\Services\ScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AuditController extends Controller
{
    public function index()
    {
        $audits = Auth::user()->audits()->latest()->paginate(15);
        return view('audits.index', compact('audits'));
    }

    public function create()
    {
        return view('audits.create');
    }

    public function store(Request $request, CrawlerService $crawler, ScannerService $scanner,
                          ReportService $reports, NotificationService $notifications)
    {
        $data = $request->validate([
            'target_url' => ['required', 'url', 'max:2048'],
            'recipients' => ['nullable', 'string', 'max:2000'],
            'authorised' => ['accepted'],
        ]);

        $recipients = $this->parseRecipients($data['recipients'] ?? '');

        $audit = Auth::user()->audits()->create([
            'target_url' => $data['target_url'],
            'recipients' => $recipients,
            'status'     => 'running',
            'started_at' => now(),
        ]);

        try {
            $crawler->discover($audit);
            $scanner->run($audit);
            $reports->generate($audit);

            $audit->update(['status' => 'completed', 'finished_at' => now()]);
            $notifications->send(Auth::user(), 'success', "Auditoria #{$audit->id} concluida.");

            $critical = $audit->fresh()->totals()['High'] ?? 0;
            if ($critical > 0) {
                $notifications->send(Auth::user(), 'critical',
                    "Foram detectadas {$critical} vulnerabilidades de risco Alto.");
            }

            if (! empty($recipients)) {
                $this->dispatchEmail($audit, $recipients, $notifications);
            }
        } catch (Throwable $e) {
            $audit->update(['status' => 'failed', 'finished_at' => now()]);
            $notifications->send(Auth::user(), 'warning',
                "Auditoria #{$audit->id} falhou: {$e->getMessage()}");
        }

        return redirect()->route('audits.show', $audit);
    }

    public function show(Audit $audit)
    {
        $this->authorize('view', $audit);
        $audit->load('pages.endpoints.vulnerabilities');

        $previous = Audit::where('user_id', $audit->user_id)
            ->where('target_url', $audit->target_url)
            ->where('id', '<', $audit->id)
            ->where('status', 'completed')
            ->latest('id')
            ->first();

        return view('audits.show', compact('audit', 'previous'));
    }

    public function destroy(Audit $audit)
    {
        $this->authorize('delete', $audit);
        $audit->delete();
        return redirect()->route('dashboard');
    }

    public function resend(Audit $audit, NotificationService $notifications)
    {
        $this->authorize('view', $audit);
        $recipients = $audit->recipients ?: [];
        if (empty($recipients)) {
            return back()->with('error', 'Esta auditoria nao tem destinatarios configurados.');
        }
        $this->dispatchEmail($audit, $recipients, $notifications);
        return back()->with('status', 'Relatorio reenviado para ' . count($recipients) . ' destinatario(s).');
    }

    public function exportCsv(Audit $audit): StreamedResponse
    {
        $this->authorize('view', $audit);
        $audit->load('pages.endpoints.vulnerabilities');

        $filename = "auditoria-{$audit->id}.csv";
        return response()->streamDownload(function () use ($audit) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Categoria OWASP', 'Risco', 'Confianca', 'Nome', 'URL', 'CWE', 'Estado', 'Notas']);
            foreach ($audit->pages as $page) {
                foreach ($page->endpoints as $endpoint) {
                    foreach ($endpoint->vulnerabilities as $v) {
                        fputcsv($out, [
                            $v->id, $v->owasp_category, $v->risk, $v->confidence,
                            $v->name, $page->url, "CWE-{$v->cwe_id}",
                            $v->status, $v->notes,
                        ]);
                    }
                }
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function compare(Audit $audit)
    {
        $this->authorize('view', $audit);
        $previous = Audit::where('user_id', $audit->user_id)
            ->where('target_url', $audit->target_url)
            ->where('id', '<', $audit->id)
            ->where('status', 'completed')
            ->latest('id')
            ->first();

        if (! $previous) {
            return back()->with('error', 'Nao existe auditoria anterior do mesmo alvo para comparar.');
        }

        $audit->load('pages.endpoints.vulnerabilities');
        $previous->load('pages.endpoints.vulnerabilities');

        return view('audits.compare', [
            'current'  => $audit,
            'previous' => $previous,
            'currentTotals'  => $audit->totals(),
            'previousTotals' => $previous->totals(),
        ]);
    }

    public function apiIndex()
    {
        return Auth::user()->audits()->latest()->paginate(20);
    }

    public function apiShow(Audit $audit)
    {
        $this->authorize('view', $audit);
        return $audit->load('pages.endpoints.vulnerabilities');
    }

    private function parseRecipients(string $raw): array
    {
        $parts = preg_split('/[\s,;]+/', trim($raw));
        $emails = [];
        foreach ($parts as $part) {
            $email = trim($part);
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = strtolower($email);
            }
        }
        return array_values(array_unique($emails));
    }

    private function dispatchEmail(Audit $audit, array $recipients, NotificationService $notifications): void
    {
        try {
            Mail::to($recipients)->send(new AuditReportMail($audit));
            $audit->update(['email_sent' => true]);
            $notifications->send(Auth::user(), 'info',
                'Relatorio enviado para ' . count($recipients) . ' destinatario(s).');
        } catch (Throwable $e) {
            $notifications->send(Auth::user(), 'warning',
                'Falha ao enviar relatorio por e-mail: ' . $e->getMessage());
        }
    }
}
