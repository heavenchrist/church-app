<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Occupation: string implements HasLabel
{
    case AccountingFinance = 'accounting_finance';
    case Administration = 'administration';
    case Agriculture = 'agriculture';
    case ArtsEntertainment = 'arts_entertainment';
    case BusinessOwner = 'business_owner';
    case Education = 'education';
    case Engineering = 'engineering';
    case Healthcare = 'healthcare';
    case InformationTechnology = 'information_technology';
    case LegalProfession = 'legal_profession';
    case Management = 'management';
    case MarketingSales = 'marketing_sales';
    case PublicService = 'public_service';
    case Research = 'research';
    case SecurityServices = 'security_services';
    case SkilledTrades = 'skilled_trades';
    case Transportation = 'transportation';
    case Hospitality = 'hospitality';
    case Student = 'student';
    case Unemployed = 'unemployed';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AccountingFinance => 'Accounting & Finance',
            self::Administration => 'Administration',
            self::Agriculture => 'Agriculture',
            self::ArtsEntertainment => 'Arts & Entertainment',
            self::BusinessOwner => 'Business Owner / Entrepreneur',
            self::Education => 'Education',
            self::Engineering => 'Engineering',
            self::Healthcare => 'Healthcare',
            self::InformationTechnology => 'Information Technology',
            self::LegalProfession => 'Legal Profession',
            self::Management => 'Management',
            self::MarketingSales => 'Marketing & Sales',
            self::PublicService => 'Public Service',
            self::Research => 'Research',
            self::SecurityServices => 'Security Services',
            self::SkilledTrades => 'Skilled Trades',
            self::Transportation => 'Transportation',
            self::Hospitality => 'Hospitality',
            self::Student => 'Student',
            self::Unemployed => 'Unemployed',
            self::Other => 'Other',
        };
    }
}
