<?php

namespace App\Filament\Resources\Ministries\RelationManagers;

use App\Enums\ExecutivePosition;
use App\Models\Member;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

class ExecutivesRelationManager extends RelationManager
{
    protected static string $relationship = 'executives';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('member_id')
                    ->label('Executive')
                    ->options(Member::query()->where('is_active', true)->pluck('full_name', 'id'))
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('position')
                    ->label('Position')
                    ->enum(ExecutivePosition::class)
                    ->options(ExecutivePosition::class)
                    ->required(),
                DatePicker::make('assigned_date')->label('Assigned Date')->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member.full_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('position')
                    ->formatStateUsing(fn ($state) => $state instanceof ExecutivePosition ? $state->getLabel() : $state)
                    ->colors(fn ($state) => match ($state) {
                        ExecutivePosition::Leader => Color::Blue,
                        ExecutivePosition::Secretary => Color::Emerald,
                        ExecutivePosition::FinancialSecretary => Color::Amber,
                        ExecutivePosition::Coordinator => Color::Violet,
                        ExecutivePosition::Organiser => Color::Rose,
                        ExecutivePosition::Assistant => Color::Cyan,
                        ExecutivePosition::ExecutiveMember => Color::Gray,
                        default => Color::Gray,
                    }),
                Tables\Columns\TextColumn::make('assigned_date')->date()->label('Assigned Date'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
