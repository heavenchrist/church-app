<?php

namespace App\Filament\Resources\Ministries\Tables;

use App\Enums\MinistryType;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

class MinistriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(' ')
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent')
                    ->sortable()
                    ->badge()
                    ->color(fn (?string $state, $record) => $record?->parent?->color ? Color::hex($record->parent->color) : null),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn ($state) => $state instanceof MinistryType ? $state : null)
                    ->badge()
                    ->color(fn ($state) => $state?->getColor())
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')->sortable()->alignCenter(),
                Tables\Columns\TextColumn::make('age_range')
                    ->sortable()
                    ->badge()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('memberCount')->counts('members')->sortable()->alignCenter(),
                Tables\Columns\ToggleColumn::make('is_active')->sortable()->alignCenter(),
                Tables\Columns\ToggleColumn::make('is_assignable')->sortable()->alignCenter()
                    ->disabled(fn ($record) => is_null($record->parent_id) &&
                    in_array($record->type, ['group', MinistryType::Traditional])
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type'),
                Tables\Filters\SelectFilter::make('gender'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
