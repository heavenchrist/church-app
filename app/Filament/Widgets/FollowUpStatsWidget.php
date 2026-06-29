<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FollowUpStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $flagged = Member::where('needs_follow_up', true)
            ->where('is_active', true)
            ->count();

        $total = Member::where('is_active', true)->count();

        $pct = $total > 0 ? round($flagged / $total * 100) : 0;

        return [
            Stat::make('Needs Follow-up', $flagged)
                ->description($flagged === 1 ? '1 member flagged' : $flagged.' members flagged')
                ->descriptionIcon('heroicon-m-phone-arrow-up-right')
                ->color($flagged > 0 ? 'warning' : 'success'),

            Stat::make('Of Active Members', $pct.'%')
                ->description($pct.'% of all active members')
                ->descriptionIcon('heroicon-m-users')
                ->color($pct > 10 ? 'danger' : ($pct > 0 ? 'warning' : 'success')),
        ];
    }
}
