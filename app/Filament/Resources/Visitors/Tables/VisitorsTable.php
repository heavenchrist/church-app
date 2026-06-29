<?php

namespace App\Filament\Resources\Visitors\Tables;

use App\Enums\Gender;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components;
use Filament\Tables;
use Filament\Tables\Table;

class VisitorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(' ')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('gender')->badge(),
                Tables\Columns\TextColumn::make('visit_date')->date(),
                Tables\Columns\TextColumn::make('invited_by')->label('Invited By'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\IconColumn::make('is_followed_up')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->options(Gender::class),
                Tables\Filters\Filter::make('visit_date')
                    ->schema([
                        Components\DatePicker::make('from')->label('From')->native(false),
                        Components\DatePicker::make('to')->label('To')->native(false),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['from'], fn ($query) => $query->whereDate('visit_date', '>=', $data['from']))
                        ->when($data['to'], fn ($query) => $query->whereDate('visit_date', '<=', $data['to']))
                    ),
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
