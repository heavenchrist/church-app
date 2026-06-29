<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Classification: string implements HasColor, HasIcon, HasLabel
{
    case Regular = 'regular';
    case Elder = 'elder';
    case Deacon = 'deacon';
    case Deaconess = 'deaconess';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Regular => 'Regular Member',
            self::Elder => 'Elder',
            self::Deacon => 'Deacon',
            self::Deaconess => 'Deaconess',
        };
    }

    public function getColor(): array|string|null
    {
        return match ($this) {
            self::Regular => Color::Gray,
            self::Elder => Color::Amber,
            self::Deacon => Color::Green,
            self::Deaconess => Color::Violet,
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Regular => 'heroicon-o-user',
            self::Elder => 'heroicon-o-star',
            self::Deacon => 'heroicon-o-shield-check',
            self::Deaconess => 'heroicon-o-heart',
        };
    }
}
