<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use Filament\Widgets\ChartWidget;

class DemographicsAgeWidget extends ChartWidget
{
    protected ?string $heading = 'Age Group Distribution';

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $labels = ['Children (<13)', 'Teens (13-19)', 'Young Adult (20-35)', 'Adult (36+)'];
        $colors = ['#6366F1', '#22C55E', '#F59E0B', '#EF4444'];

        $members = Member::where('is_active', true)->get(['date_of_birth']);
        $groups = ['children' => 0, 'teens' => 0, 'young_adult' => 0, 'adult' => 0];

        foreach ($members as $member) {
            if (! $member->date_of_birth) {
                continue;
            }
            $groups[$member->age_group]++;
        }

        return [
            'datasets' => [
                [
                    'data' => array_values($groups),
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'y' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
