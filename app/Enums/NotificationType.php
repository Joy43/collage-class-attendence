<?php

namespace App\Enums;

enum NotificationType: string
{
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';

    public function label(): string
    {
        return match ($this) {
            self::Create => 'Created',
            self::Update => 'Updated',
            self::Delete => 'Deleted',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Create => '➕',
            self::Update => '✏️',
            self::Delete => '🗑️',
        };
    }

    public function textClass(): string
    {
        return match ($this) {
            self::Create => 'text-present',
            self::Update => 'text-primary',
            self::Delete => 'text-absent',
        };
    }

    public function softClass(): string
    {
        return match ($this) {
            self::Create => 'bg-present-soft',
            self::Update => 'bg-primary-soft',
            self::Delete => 'bg-absent-soft',
        };
    }

    public function ringClass(): string
    {
        return match ($this) {
            self::Create => 'border-present/20',
            self::Update => 'border-primary/20',
            self::Delete => 'border-absent/20',
        };
    }
}
