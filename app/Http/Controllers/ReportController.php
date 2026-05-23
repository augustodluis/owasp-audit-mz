<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function show(Audit $audit)
    {
        $this->authorize('view', $audit);
        $audit->load('pages.endpoints.vulnerabilities');
        return view('reports.show', compact('audit'));
    }

    public function pdf(Audit $audit)
    {
        $this->authorize('view', $audit);
        $audit->load('pages.endpoints.vulnerabilities');
        $pdf = Pdf::loadView('reports.pdf', compact('audit'));
        return $pdf->download("auditoria-{$audit->id}.pdf");
    }
}
