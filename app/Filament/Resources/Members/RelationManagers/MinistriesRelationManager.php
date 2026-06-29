<?php

namespace App\Filament\Resources\Members\RelationManagers;

use App\Enums\MinistryType;
use App\Models\Ministry;
use Filament\Forms\Components;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MinistriesRelationManager extends RelationManager
{
    protected static string $relationship = 'ministries';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Select::make('ministry_id')
                    ->label('Ministry')
                    ->options(Ministry::query()->where('is_active', true)->pluck('name', 'id'))
                    ->preload()
                    ->searchable()
                    ->required(),
                Components\TextInput::make('role')->default('member'),
                Components\DatePicker::make('joined_date')->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('pivot.joined_date')->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(MinistryType::class),
            ]);
    }
}
