<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'target_url', 'recipients', 'email_sent', 'status', 'started_at', 'finished_at'];
    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'recipients' => 'array',
        'email_sent' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function report()
    {
        return $this->hasOne(Report::class);
    }

    public function totals(): array
    {
        $totals = ['High' => 0, 'Medium' => 0, 'Low' => 0, 'Informational' => 0];
        $this->loadMissing('pages.endpoints.vulnerabilities');
        foreach ($this->pages as $page) {
            foreach ($page->endpoints as $endpoint) {
                foreach ($endpoint->vulnerabilities as $vuln) {
                    if (isset($totals[$vuln->risk])) {
                        $totals[$vuln->risk]++;
                    }
                }
            }
        }
        return $totals;
    }
}
