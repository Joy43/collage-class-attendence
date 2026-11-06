<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Present = 'Present';
    case Absent = 'Absent';
    case Late = 'Late';
    case Leave = 'Leave';

    public function emoji(): string
    {
        return match ($this) {
            self::Present => '✅',
            self::Absent => '❌',
            self::Late => '⏰',
            self::Leave => '📝',
        };
    }

    /** Text colour utility, e.g. for counts. */
    public function textClass(): string
    {
        return match ($this) {
            self::Present => 'text-present',
            self::Absent => 'text-absent',
            self::Late => 'text-late',
            self::Leave => 'text-leave',
        };
    }

    /** Solid dot / bar colour utility. */
    public function bgClass(): string
    {
        return match ($this) {
            self::Present => 'bg-present',
            self::Absent => 'bg-absent',
            self::Late => 'bg-late',
            self::Leave => 'bg-leave',
        };
    }

    /** Left-border accent used on summary tiles. */
    public function borderClass(): string
    {
        return match ($this) {
            self::Present => 'border-l-present',
            self::Absent => 'border-l-absent',
            self::Late => 'border-l-late',
            self::Leave => 'border-l-leave',
        };
    }

    /** Soft pill: tinted background + coloured text + border. */
    public function chipClass(): string
    {
        return match ($this) {
            self::Present => 'bg-present-soft text-present border-present/25',
            self::Absent => 'bg-absent-soft text-absent border-absent/25',
            self::Late => 'bg-late-soft text-late border-late/25',
            self::Leave => 'bg-leave-soft text-leave border-leave/25',
        };
    }

    /** Ring colour applied when the chip is selected on the attendance form. */
    public function ringClass(): string
    {
        return match ($this) {
            self::Present => 'data-[selected=true]:ring-present',
            self::Absent => 'data-[selected=true]:ring-absent',
            self::Late => 'data-[selected=true]:ring-late',
            self::Leave => 'data-[selected=true]:ring-leave',
        };
    }

    /** Hex colour for charts. */
    public function hex(): string
    {
        return match ($this) {
            self::Present => '#16A34A',
            self::Absent => '#DC2626',
            self::Late => '#EA580C',
            self::Leave => '#7C3AED',
        };
    }
}
