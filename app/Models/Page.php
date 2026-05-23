<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public $timestamps = false;

    protected $fillable = ['audit_id', 'url', 'http_status', 'discovered_at'];
    protected $casts = ['discovered_at' => 'datetime'];

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    public function endpoints()
    {
        return $this->hasMany(Endpoint::class);
    }
}
