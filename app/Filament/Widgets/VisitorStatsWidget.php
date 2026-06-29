<?php

namespace App\Filament\Widgets;

use App\Enums\VisitorStatus;
use App\Models\Visitor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VisitorStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $query = Visitor::query();

        $filters = $this->tableFilters ?? [];

        $from = $filters['visit_date']['from'] ?? now()->startOfMonth()->toDateString();
        $to = $filters['visit_date']['to'] ?? now()->endOfMonth()->toDateString();
        $gender = $filters['gender'] ?? null;

        $query->whereDate('visit_date', '>=', $from)
            ->whereDate('visit_date', '<=', $to);

        if ($gender) {
            $query->where('gender', $gender);
        }

        $total = (clone $query)->count();
        $male = (clone $query)->where('gender', 'male')->count();
        $female = (clone $query)->where('gender', 'female')->count();
        $followedUp = (clone $query)->where('is_followed_up', true)->count();
        $converted = (clone $query)->where('status', VisitorStatus::BecameConvert)->count();
        $conversionPct = $total > 0 ? round($converted / $total * 100) : 0;

        return [
            Stat::make('Total Visitors', $total)
                ->description('All visitors')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Male', $male)
                ->description('Male visitors')
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),

            Stat::make('Female', $female)
                ->description('Female visitors')
                ->descriptionIcon('heroicon-m-user')
                ->color('warning'),

            Stat::make('Followed Up', $followedUp)
                ->description('Followed up')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Became Convert', $converted)
                ->description($conversionPct.'% conversion rate')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
        ];
    }
}
