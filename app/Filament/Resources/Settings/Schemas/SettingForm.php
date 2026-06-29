<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Setting Details')->components([
                    Grid::make(2)->schema([
                        Components\TextInput::make('key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Components\Select::make('type')
                            ->options([
                                'text' => 'Text',
                                'textarea' => 'Textarea',
                                'boolean' => 'Boolean',
                                'image' => 'Image',
                                'select' => 'Select',
                            ])
                            ->searchable()
                            ->preload()
                            ->default('text'),
                    ]),
                    Components\TextInput::make('group')
                        ->maxLength(255),
                    Components\Textarea::make('value')
                        ->rows(3),
                ]),
            ]);
    }
}
