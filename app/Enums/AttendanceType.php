<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AttendanceType: string implements HasColor, HasIcon, HasLabel
{
    case Present = 'present';
    case Absent = 'absent';
    case Late = 'late';
    case Excused = 'excused';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Present => 'Present',
            self::Absent => 'Absent',
            self::Late => 'Late',
            self::Excused => 'Excused',
        };
    }

    public function getColor(): array|string|null
    {
        return match ($this) {
            self::Present => Color::Green,
            self::Absent => Color::Red,
            self::Late => Color::Amber,
            self::Excused => Color::Gray,
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Present => 'heroicon-o-check-circle',
            self::Absent => 'heroicon-o-x-circle',
            self::Late => 'heroicon-o-clock',
            self::Excused => 'heroicon-o-question-mark-circle',
        };
    }
}
