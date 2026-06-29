<?php

namespace App\Filament\Resources\BibleStudyGroups\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class BibleStudyGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(' ')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('description')->limit(30),
                Tables\Columns\TextColumn::make('leader.full_name')
                    ->label('Leader'),
                Tables\Columns\TextColumn::make('memberCount')->counts('members'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
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
