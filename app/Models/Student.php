<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['roll_no', 'name'])]
class Student extends Model
{
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /** Percentage of recorded sessions marked Present (0 when none). */
    public function attendancePercent(): int
    {
        $total = $this->attendances_count ?? $this->attendances()->count();
        $present = $this->present_count ?? $this->attendances()->where('status', 'Present')->count();

        return $total > 0 ? (int) round($present / $total * 100) : 0;
    }

    public function initial(): string
    {
        return strtoupper(mb_substr($this->name, 0, 1));
    }
}
