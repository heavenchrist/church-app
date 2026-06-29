<?php

namespace App\Filament\Resources\Members\RelationManagers;

use App\Enums\AttendanceType;
use Filament\Forms\Components;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Select::make('service_id')
                    ->relationship('service', 'title')
                    ->required(),
                Components\Select::make('attendance_type')
                    ->options(AttendanceType::class),
                Components\Textarea::make('remarks'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.title'),
                Tables\Columns\TextColumn::make('service.service_date')->label('Date'),
                Tables\Columns\TextColumn::make('attendance_type')->badge(),
            ]);
    }
}
