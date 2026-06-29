<?php

namespace App\Filament\Resources\Ministries\RelationManagers;

use App\Models\Member;
use Filament\Forms\Components;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Select::make('member_id')
                    ->label('Member')
                    ->options(Member::query()->where('is_active', true)->pluck('full_name', 'id'))
                    ->preload()
                    ->searchable()
                    ->required(),
                Components\TextInput::make('pivot.role')->default('member'),
                Components\DatePicker::make('pivot.joined_date')->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo')->circular()->size(40),
                Tables\Columns\TextColumn::make('full_name')->searchable(),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('pivot.role'),
            ]);
    }
}
