<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = ['password'];

    public function routeNotificationForSms(): string
    {
        return $this->mobile;
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}
