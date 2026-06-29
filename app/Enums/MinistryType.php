<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MinistryType: string implements HasColor, HasIcon, HasLabel
{
    case Traditional = 'traditional';
    case MinistryGroup = 'ministry_group';

    case Group = 'group';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Traditional => 'Traditional Ministry',
            self::MinistryGroup => 'Ministry Group',
            self::Group => 'Other Group',

        };
    }

    public function getColor(): array|string|null
    {
        return match ($this) {
            self::Traditional => Color::Blue,
            self::MinistryGroup => Color::Purple,
            self::Group => Color::Green,
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Traditional => 'heroicon-o-building-library',
            self::MinistryGroup => 'heroicon-o-flag',
            self::Group => 'heroicon-o-user-group',
        };
    }
}
