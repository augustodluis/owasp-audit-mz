<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Endpoint extends Model
{
    public $timestamps = false;

    protected $fillable = ['page_id', 'method', 'parameters'];
    protected $casts = ['parameters' => 'array'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function vulnerabilities()
    {
        return $this->hasMany(Vulnerability::class);
    }
}
