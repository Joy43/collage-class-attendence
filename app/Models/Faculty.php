<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/** A faculty member — also the application's login user. */
#[Fillable(['user_name', 'name', 'password'])]
#[Hidden(['password', 'remember_token'])]
class Faculty extends Authenticatable
{
    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function activityNotifications(): HasMany
    {
        return $this->hasMany(ActivityNotification::class);
    }

    public function initials(): string
    {
        return strtoupper(substr($this->user_name, 0, 2));
    }
}
