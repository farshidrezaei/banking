<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SmsLog extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_delivered' => 'boolean'
    ];
}
