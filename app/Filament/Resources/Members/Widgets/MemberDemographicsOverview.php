<?php

namespace App\Filament\Resources\Members\Widgets;

use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MemberDemographicsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            $this->male(),
            $this->female(),
            $this->regularMembers(),
            $this->officers(),
        ];
    }

    private function male(): Stat
    {
        $total = Member::where('is_active', true)->where('gender', 'male')->count();

        return Stat::make('Male', $total)
            ->description('Active male members')
            ->descriptionIcon('heroicon-m-user-circle')
            ->color('info');
    }

    private function female(): Stat
    {
        $total = Member::where('is_active', true)->where('gender', 'female')->count();

        return Stat::make('Female', $total)
            ->description('Active female members')
            ->descriptionIcon('heroicon-m-user')
            ->color('warning');
    }

    private function regularMembers(): Stat
    {
        $total = Member::where('is_active', true)
            ->where('classification', 'regular')
            ->count();

        return Stat::make('Regular Members', $total)
            ->description('Regular member classification')
            ->descriptionIcon('heroicon-m-user-group')
            ->color('gray');
    }

    private function officers(): Stat
    {
        $elders = Member::where('is_active', true)->where('classification', 'elder')->count();
        $deacons = Member::where('is_active', true)->where('classification', 'deacon')->count();
        $deaconesses = Member::where('is_active', true)->where('classification', 'deaconess')->count();
        $total = $elders + $deacons + $deaconesses;

        return Stat::make('Officers', $total)
            ->description("Elders: {$elders} | Deacons: {$deacons} | Deaconesses: {$deaconesses}")
            ->descriptionIcon('heroicon-m-shield-check')
            ->color('success');
    }
}
