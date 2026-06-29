<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use App\Models\Service;
use App\Models\Visitor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ChurchStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $memberTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $memberTrend[] = Member::where('is_active', true)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        $visitorTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $visitorTrend[] = Visitor::whereDate('visit_date', $day)->count();
        }

        $serviceTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            $serviceTrend[] = Service::whereBetween('service_date', [$weekStart, $weekEnd])->count();
        }

        return [
            Stat::make('Total Members', Member::where('is_active', true)->count())
                ->description('Active members')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart($memberTrend),

            Stat::make('This Month Visitors', Visitor::whereMonth('visit_date', now()->month)->count())
                ->description('Visitors this month')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info')
                ->chart($visitorTrend),

            Stat::make('Services', Service::whereMonth('service_date', now()->month)->count())
                ->description('Services this month')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->chart($serviceTrend),
        ];
    }
}
