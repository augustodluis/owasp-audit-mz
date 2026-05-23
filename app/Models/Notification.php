<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'message', 'read_flag'];
    protected $casts = ['read_flag' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
