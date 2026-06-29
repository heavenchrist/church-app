<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use Filament\Widgets\ChartWidget;

class DemographicsGenderWidget extends ChartWidget
{
    protected ?string $heading = 'Gender Distribution';

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $male = Member::where('is_active', true)->where('gender', 'male')->count();
        $female = Member::where('is_active', true)->where('gender', 'female')->count();

        return [
            'datasets' => [
                [
                    'data' => [$male, $female],
                    'backgroundColor' => ['#6366F1', '#F43F5E'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['Male ('.$male.')', 'Female ('.$female.')'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
