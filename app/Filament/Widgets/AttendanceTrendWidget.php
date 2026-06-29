<?php

namespace App\Filament\Widgets;

use App\Enums\AttendanceType;
use App\Models\Attendance;
use App\Models\Service;
use Filament\Widgets\ChartWidget;

class AttendanceTrendWidget extends ChartWidget
{
    protected ?string $heading = 'Attendance Trend';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $labels = [];
        $presentData = [];
        $absentData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');

            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $serviceIds = Service::whereBetween('service_date', [$start, $end])
                ->pluck('id');

            if ($serviceIds->isEmpty()) {
                $presentData[] = 0;
                $absentData[] = 0;

                continue;
            }

            $presentData[] = Attendance::whereIn('service_id', $serviceIds)
                ->where('attendance_type', AttendanceType::Present->value)
                ->count();

            $absentData[] = Attendance::whereIn('service_id', $serviceIds)
                ->where('attendance_type', AttendanceType::Absent->value)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Present',
                    'data' => $presentData,
                    'backgroundColor' => '#22C55E',
                    'borderColor' => '#22C55E',
                    'tension' => 0.3,
                    'fill' => false,
                ],
                [
                    'label' => 'Absent',
                    'data' => $absentData,
                    'backgroundColor' => '#EF4444',
                    'borderColor' => '#EF4444',
                    'tension' => 0.3,
                    'fill' => false,
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
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
