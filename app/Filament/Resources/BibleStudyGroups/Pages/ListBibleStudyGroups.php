<?php

namespace App\Filament\Resources\BibleStudyGroups\Pages;

use App\Filament\Resources\BibleStudyGroups\BibleStudyGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBibleStudyGroups extends ListRecords
{
    protected static string $resource = BibleStudyGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
