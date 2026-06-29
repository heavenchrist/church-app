<?php

namespace App\Filament\Widgets;

use App\Models\Visitor;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class VisitorChartWidget extends ChartWidget
{
    protected ?string $heading = 'Visitors Over Time';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $filters = $this->tableFilters ?? [];

        $days = (int) ($this->filter ?? 30);
        $from = $filters['visit_date']['from'] ?? now()->subDays($days)->toDateString();
        $to = $filters['visit_date']['to'] ?? now()->toDateString();
        $gender = $filters['gender'] ?? null;

        $query = Visitor::query()
            ->whereDate('visit_date', '>=', $from)
            ->whereDate('visit_date', '<=', $to);

        if ($gender) {
            $query->where('gender', $gender);
        }

        $data = $query->selectRaw('DATE(visit_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = $data->pluck('date')->map(fn ($date) => Carbon::parse($date)->format('M d'))->toArray();
        $counts = $data->pluck('count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Visitors',
                    'data' => $counts,
                    'backgroundColor' => '#6366F1',
                    'borderColor' => '#6366F1',
                    'borderRadius' => 6,
                    'barPercentage' => 0.6,
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
