<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ServiceType: string implements HasColor, HasLabel
{
    case SundayService = 'sunday_service';
    case Midweek = 'midweek';
    case BibleStudy = 'bible_study';
    case PrayerMeeting = 'prayer_meeting';
    case MinistryService = 'ministry_service';
    case SpecialService = 'special_service';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SundayService => 'Sunday Service',
            self::Midweek => 'Midweek Service',
            self::BibleStudy => 'Bible Study',
            self::PrayerMeeting => 'Prayer Meeting',
            self::MinistryService => 'Ministry Service',
            self::SpecialService => 'Special Service',
            self::Other => 'Other',
        };
    }

    public function getColor(): ?array
    {
        return match ($this) {
            self::SundayService => Color::Emerald, // 'primary',
            self::Midweek => Color::Sky,
            self::BibleStudy => Color::Rose,
            self::PrayerMeeting => Color::Olive,
            self::MinistryService => Color::Purple,
            self::SpecialService => Color::Amber,
            self::Other => Color::Purple,
        };
    }
}
