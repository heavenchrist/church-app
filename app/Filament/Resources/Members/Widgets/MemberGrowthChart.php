<?php

namespace App\Filament\Resources\Members\Widgets;

use App\Models\Member;
use Filament\Widgets\ChartWidget;

class MemberGrowthChart extends ChartWidget
{
    protected ?string $heading = 'Membership Growth';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $months = collect(range(11, 0))->map(function ($i) {
            $date = now()->subMonths($i);

            return [
                'label' => $date->format('M Y'),
                'year' => $date->year,
                'month' => $date->month,
            ];
        });

        $monthlyJoins = Member::where('is_active', true)
            ->where('date_joined', '>=', now()->subMonths(12)->startOfMonth())
            ->selectRaw('YEAR(date_joined) as year, MONTH(date_joined) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->keyBy(fn ($item) => "{$item->year}-{$item->month}");

        $cumulative = 0;
        $labels = [];
        $data = [];

        foreach ($months as $m) {
            $key = "{$m['year']}-{$m['month']}";
            $count = $monthlyJoins->get($key)?->count ?? 0;
            $cumulative += $count;
            $labels[] = $m['label'];
            $data[] = $cumulative;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Members',
                    'data' => $data,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'borderColor' => '#6366F1',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0,0,0,0.05)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}
