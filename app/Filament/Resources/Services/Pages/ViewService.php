<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewService extends ViewRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('takeAttendance')
                ->label('Take Attendance')
                ->icon('heroicon-o-clipboard-document-check')
                ->url(fn () => ServiceResource::getUrl('take-attendance', ['record' => $this->record])),
        ];
    }
}
