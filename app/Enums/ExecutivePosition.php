<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ExecutivePosition: string implements HasLabel
{
    case Leader = 'leader';
    case Assistant = 'assistant';
    case ExecutiveMember = 'executive_member';
    case Organiser = 'organiser';
    case Secretary = 'secretary';
    case FinancialSecretary = 'financial_secretary';
    case Coordinator = 'coordinator';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Leader => 'Leader',
            self::Assistant => 'Assistant',
            self::ExecutiveMember => 'Executive Member',
            self::Organiser => 'Organiser',
            self::Secretary => 'Secretary',
            self::FinancialSecretary => 'Financial Secretary',
            self::Coordinator => 'Coordinator',
        };
    }
}
