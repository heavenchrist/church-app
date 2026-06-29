<?php

namespace App\Filament\Resources\Services\Tables;

use App\Enums\ServiceType;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(' ')
            ->columns([
                Tables\Columns\TextColumn::make('topic')->searchable(),
                Tables\Columns\TextColumn::make('service_date')->date(),
                Tables\Columns\TextColumn::make('service_type')->formatStateUsing(fn (ServiceType $state): string => $state->getLabel())->badge()->alignCenter(),
                Tables\Columns\TextColumn::make('ministry.name'),
                Tables\Columns\TextColumn::make('attendances_count')->badge()->alignCenter()->counts('attendances'),
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
