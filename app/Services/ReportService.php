<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\Report;

class ReportService
{
    public function generate(Audit $audit): Report
    {
        return Report::updateOrCreate(
            ['audit_id' => $audit->id],
            ['generated_at' => now(), 'format' => 'html']
        );
    }
}
