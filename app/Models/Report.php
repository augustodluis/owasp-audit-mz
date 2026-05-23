<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $timestamps = false;

    protected $fillable = ['audit_id', 'generated_at', 'format', 'file_path'];
    protected $casts = ['generated_at' => 'datetime'];

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }
}
