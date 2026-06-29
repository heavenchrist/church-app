<?php

namespace App\Filament\Resources\Members\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(' ')
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo')->circular()->size(40),
                Tables\Columns\TextColumn::make('member_id')->searchable(),
                Tables\Columns\TextColumn::make('full_name')->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->badge(),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('bibleStudyGroup.name'),
                Tables\Columns\TextColumn::make('classification')->badge(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->icons([
                        'visitor' => 'heroicon-o-user-plus',
                        'convert' => 'heroicon-o-sparkles',
                        'member' => 'heroicon-o-check-circle',
                        'inactive' => 'heroicon-o-x-circle',
                        'demised' => 'heroicon-o-heart',
                    ]),
                Tables\Columns\TextColumn::make('assignedTo.full_name')
                    ->label('Assigned To')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('gender'),
                Tables\Filters\SelectFilter::make('ministries')
                    ->relationship('ministries', 'name')
                    ->preload(),
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
