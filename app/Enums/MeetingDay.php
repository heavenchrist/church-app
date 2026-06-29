<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MeetingDay: string implements HasColor, HasLabel
{
    case Sunday = 'sunday';
    case Monday = 'monday';
    case Tuesday = 'tuesday';
    case Wednesday = 'wednesday';
    case Thursday = 'thursday';
    case Friday = 'friday';
    case Saturday = 'saturday';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Sunday => 'Sunday',
            self::Monday => 'Monday',
            self::Tuesday => 'Tuesday',
            self::Wednesday => 'Wednesday',
            self::Thursday => 'Thursday',
            self::Friday => 'Friday',
            self::Saturday => 'Saturday',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Sunday => 'danger',
            self::Monday => 'gray',
            self::Tuesday => 'info',
            self::Wednesday => 'warning',
            self::Thursday => 'success',
            self::Friday => 'purple',
            self::Saturday => 'primary',
        };
    }
}
