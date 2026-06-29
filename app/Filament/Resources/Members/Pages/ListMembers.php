<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Filament\Resources\Members\Widgets\MemberDemographicsOverview;
use App\Filament\Resources\Members\Widgets\MemberGrowthChart;
use App\Filament\Resources\Members\Widgets\MemberStatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MemberStatsOverview::class,
            MemberDemographicsOverview::class,
           // MemberGrowthChart::class,
        ];
    }
}
