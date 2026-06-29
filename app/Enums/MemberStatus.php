<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MemberStatus: string implements HasColor, HasIcon, HasLabel
{
    // Create:
    case Convert = 'convert';
    case Member = 'member';
    case TransferIn = 'transfer_in';

    // Update:
    case TransferOut = 'transfer_out';
    case Inactive = 'inactive';
    case Demised = 'demised';

    // Visitor:
    case Visitor = 'visitor';

    public static function createOptions(): array
    {
        return [
            self::Convert,
            self::Member,
            self::TransferIn,
        ];
    }

    public static function updateOptions(): array
    {
        return [
            self::Convert,
            self::Member,
            self::TransferIn,
            self::TransferOut,
            self::Inactive,
            self::Demised,
        ];
    }

    public static function visitorOptions(): array
    {
        return [
            self::Visitor,
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TransferIn => 'Transfer In',
            self::TransferOut => 'Transfer Out',
            self::Visitor => 'Visitor',
            self::Convert => 'Convert',
            self::Member => 'Member',
            self::Inactive => 'Inactive',
            self::Demised => 'Demised',
        };
    }

    public function getColor(): array|string|null
    {
        return match ($this) {
            self::TransferIn => Color::Sky,
            self::TransferOut => Color::Amber,
            self::Visitor => Color::Orange,
            self::Convert => Color::Green,
            self::Member => Color::Blue,
            self::Inactive => Color::Gray,
            self::Demised => Color::Gray,
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::TransferIn => 'heroicon-o-user-plus',
            self::TransferOut => 'heroicon-o-user-minus',
            self::Visitor => 'heroicon-o-user-plus',
            self::Convert => 'heroicon-o-sparkles',
            self::Member => 'heroicon-o-check-circle',
            self::Inactive => 'heroicon-o-x-circle',
            self::Demised => 'heroicon-o-heart',
        };
    }
}
