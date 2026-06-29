<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Gender: string implements HasColor, HasIcon, HasLabel
{
    case Male = 'male';
    case Female = 'female';
    case Both = 'both';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Male => 'Male',
            self::Female => 'Female',
            self::Both => 'Both',
        };
    }

    public function getColor(): array|string|null
    {
        return match ($this) {
            self::Male => Color::Blue,
            self::Female => Color::Pink,
            self::Both => Color::Violet,
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Male => 'heroicon-o-user-circle',
            self::Female => 'heroicon-o-user',
            self::Both => 'heroicon-o-users',
        };
    }

    public static function memberOptions(): array
    {
        return collect(self::cases())
            ->filter(fn ($case) => $case !== self::Both)
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
