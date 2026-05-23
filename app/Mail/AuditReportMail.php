<?php

namespace App\Mail;

use App\Models\Audit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuditReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Audit $audit)
    {
    }

    public function envelope(): Envelope
    {
        $host = parse_url($this->audit->target_url, PHP_URL_HOST) ?: $this->audit->target_url;
        return new Envelope(
            subject: "Relatorio de auditoria #{$this->audit->id} - {$host}",
        );
    }

    public function content(): Content
    {
        $this->audit->loadMissing('pages.endpoints.vulnerabilities');
        return new Content(
            view: 'emails.audit_report',
            with: [
                'audit'  => $this->audit,
                'totals' => $this->audit->totals(),
            ],
        );
    }

    public function attachments(): array
    {
        $this->audit->loadMissing('pages.endpoints.vulnerabilities');
        $pdf = Pdf::loadView('reports.pdf', ['audit' => $this->audit]);
        return [
            Attachment::fromData(fn () => $pdf->output(), "auditoria-{$this->audit->id}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
