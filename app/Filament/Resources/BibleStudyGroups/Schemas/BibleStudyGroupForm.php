<?php

namespace App\Filament\Resources\BibleStudyGroups\Schemas;

use App\Models\Member;
use Filament\Forms\Components;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BibleStudyGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Group Details')
                    ->schema([
                        Components\TextInput::make('name')->required(),
                        Components\Textarea::make('description'),
                        Components\Select::make('leader_id')->label('Leader')
                            ->options(fn () => Member::orderBy('full_name')->get()->mapWithKeys(fn ($m) => [$m->id => $m->full_name]))
                            ->searchable()
                            ->preload()
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'This member is already assigned as leader of another group.',
                            ]),
                        Components\Toggle::make('is_active')->default(true)->inline(false),
                    ]),
            ]);
    }
}
