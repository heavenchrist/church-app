<?php

namespace App\Filament\Resources\SliderImages\Schemas;

use Filament\Forms\Components;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SliderImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Image Details')->components([
                    Grid::make(2)->schema([
                        Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Components\TextInput::make('order')
                            ->numeric()
                            ->default(0),
                    ]),
                    Components\TextInput::make('description')
                        ->maxLength(500),
                    Components\TextInput::make('link')
                        ->url()
                        ->maxLength(500),
                    Components\FileUpload::make('image')
                        ->label('Slider Image')
                        ->helperText('Recommended size: 1920x600 pixels')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('slide-images')
                        ->maxSize(512),
                    Components\Toggle::make('is_active')
                        ->label('Active')
                        ->inline(false)
                        ->default(true),
                ])->columnSpanFull(),
            ]);
    }
}
