<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'level', 'message', 'stack_trace', 'context',
        'file', 'line', 'user_id', 'created_at',
    ];

    protected $casts = ['context' => 'array', 'created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
