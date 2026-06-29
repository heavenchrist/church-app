<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BirthdayStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $now = now();

        return [
            $this->todayCount($now),
            $this->thisWeekCount($now),
            $this->thisMonthCount($now),
        ];
    }

    private function todayCount($now): Stat
    {
        $count = Member::where('is_active', true)
            ->whereMonth('date_of_birth', $now->month)
            ->whereDay('date_of_birth', $now->day)
            ->count();

        return Stat::make("Today's Birthdays", $count)
            ->description($now->format('F j'))
            ->descriptionIcon('heroicon-m-calendar')
            ->color('primary');
    }

    private function thisWeekCount($now): Stat
    {
        $start = $now->format('m-d');
        $end = $now->copy()->addDays(6)->format('m-d');

        $count = Member::where('is_active', true)
            ->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') BETWEEN ? AND ?", [$start, $end])
            ->count();

        return Stat::make("This Week's Birthdays", $count)
            ->description($now->format('M j').' – '.$now->copy()->addDays(6)->format('M j'))
            ->descriptionIcon('heroicon-m-calendar-days')
            ->color('info');
    }

    private function thisMonthCount($now): Stat
    {
        $count = Member::where('is_active', true)
            ->whereMonth('date_of_birth', $now->month)
            ->count();

        return Stat::make("This Month's Birthdays", $count)
            ->description($now->format('F'))
            ->descriptionIcon('heroicon-m-cake')
            ->color('success');
    }
}
