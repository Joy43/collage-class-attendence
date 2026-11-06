<?php

namespace App\Models;

use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/** An in-app log entry shown in the notification bell (student created/updated/deleted). */
#[Fillable(['faculty_id', 'type', 'title', 'message', 'is_read'])]
class ActivityNotification extends Model
{
    protected function casts(): array
    {
        return [
            'type' => NotificationType::class,
            'is_read' => 'boolean',
        ];
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function scopeForFaculty(Builder $query, int $facultyId): Builder
    {
        return $query->where('faculty_id', $facultyId);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /** Record an entry for the currently signed-in faculty member. */
    public static function record(NotificationType $type, string $title, string $message): self
    {
        return static::create([
            'faculty_id' => Auth::id(),
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ]);
    }
}
