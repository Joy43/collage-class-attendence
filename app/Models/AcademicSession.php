<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['year', 'term'])]
class AcademicSession extends Model
{
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function label(): string
    {
        return "{$this->year} — {$this->term}";
    }
}
