<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'title', 'credit'])]
class Course extends Model
{
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function label(): string
    {
        return "{$this->code} — {$this->title}";
    }
}
