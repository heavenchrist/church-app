<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class RoleRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('name')
                    ->label('Role')
                    ->required()
                    ->options(fn () => \Spatie\Permission\Models\Role::query()
                        ->when(
                            ! auth()->user()?->hasRole('super_admin'),
                            fn ($q) => $q->where('name', '!=', 'super_admin'),
                        )
                        ->pluck('name', 'id')
                    ),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('guard_name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->recordSelectOptionsQuery(fn ($query) => $query->when(
                        ! auth()->user()?->hasRole('super_admin'),
                        fn ($q) => $q->where('name', '!=', 'super_admin'),
                    )),
            ])
            ->actions([
                DetachAction::make()
                    ->hidden(fn ($record) => $record->name === 'super_admin'
                        && ! auth()->user()?->hasRole('super_admin')),
            ]);
    }
}
