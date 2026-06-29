<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum VisitorStatus: string implements HasColor, HasLabel
{
    case FirstVisit = 'first_visit';
    case SecondVisit = 'second_visit';
    case Interested = 'interested';
    case NotInterested = 'not_interested';
    case BecameConvert = 'became_convert';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FirstVisit => 'First Visit',
            self::SecondVisit => 'Second Visit',
            self::Interested => 'Interested',
            self::NotInterested => 'Not Interested',
            self::BecameConvert => 'Became Convert',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::FirstVisit => 'info',
            self::SecondVisit => 'primary',
            self::Interested => 'success',
            self::NotInterested => 'gray',
            self::BecameConvert => 'warning',
        };
    }
}
