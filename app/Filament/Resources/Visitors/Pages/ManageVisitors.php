<?php

namespace App\Filament\Resources\Visitors\Pages;

use App\Filament\Resources\Visitors\VisitorResource;
use App\Filament\Widgets\VisitorChartWidget;
use App\Filament\Widgets\VisitorStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;

class ManageVisitors extends ManageRecords
{
    protected static string $resource = VisitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth(Width::ExtraLarge)
                ->closeModalByClickingAway(false),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            VisitorStatsWidget::class,
            VisitorChartWidget::class,
        ];
    }
}
