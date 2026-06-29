<?php

namespace App\Filament\Resources\Members\Widgets;

use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MemberStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            $this->totalActiveMembers(),
            $this->newThisMonth(),
            $this->waterBaptized(),
            $this->inMinistries(),
        ];
    }

    private function totalActiveMembers(): Stat
    {
        $count = Member::where('is_active', true)->count();

        $weeklyJoins = Member::where('is_active', true)
            ->where('date_joined', '>=', now()->subWeeks(6)->startOfWeek())
            ->selectRaw('WEEK(date_joined, 1) as week, COUNT(*) as count')
            ->groupBy('week')
            ->orderBy('week')
            ->pluck('count')
            ->toArray();

        return Stat::make('Total Active Members', $count)
            ->description('Active members in the church')
            ->descriptionIcon('heroicon-m-users')
            ->color('success')
            ->chart($weeklyJoins ?: [0]);
    }

    private function newThisMonth(): Stat
    {
        $thisMonth = Member::whereMonth('date_joined', now()->month)
            ->whereYear('date_joined', now()->year)
            ->count();

        $lastMonth = Member::whereMonth('date_joined', now()->subMonth()->month)
            ->whereYear('date_joined', now()->subMonth()->year)
            ->count();

        $change = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : ($thisMonth > 0 ? 100 : 0);

        $changeLabel = $change > 0 ? "{$change}% increase" : "{$change}% decrease";

        return Stat::make('New This Month', $thisMonth)
            ->description("vs last month: {$changeLabel}")
            ->descriptionIcon('heroicon-m-user-plus')
            ->color($change >= 0 ? 'info' : 'warning');
    }

    private function waterBaptized(): Stat
    {
        $total = Member::whereNotNull('water_baptism_date')->count();

        $thisMonth = Member::whereNotNull('water_baptism_date')
            ->whereMonth('water_baptism_date', now()->month)
            ->whereYear('water_baptism_date', now()->year)
            ->count();

        return Stat::make('Water Baptized', $total)
            ->description("{$thisMonth} this month")
            ->descriptionIcon('heroicon-m-check-badge')
            ->color('primary');
    }

    private function inMinistries(): Stat
    {
        $total = Member::where('is_active', true)
            ->whereHas('ministries')
            ->count();

        $activeMembers = Member::where('is_active', true)->count();
        $percentage = $activeMembers > 0
            ? round(($total / $activeMembers) * 100, 1)
            : 0;

        return Stat::make('In Ministries', $total)
            ->description("{$percentage}% of active members")
            ->descriptionIcon('heroicon-m-briefcase')
            ->color('warning');
    }
}
