<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DemographicsAgeWidget;
use App\Filament\Widgets\DemographicsGenderWidget;
use Filament\Pages\Page;
use UnitEnum;

class MemberDemographics extends Page
{
    protected static ?string $navigationLabel = 'Member Demographics';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'member-demographics';

    protected string $view = 'filament.pages.member-demographics';

    protected function getHeaderWidgets(): array
    {
        return [
            DemographicsGenderWidget::class,
            DemographicsAgeWidget::class,
        ];
    }
}
