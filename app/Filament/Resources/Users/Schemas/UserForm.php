<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Member;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Details')
                    ->components([
                        Select::make('member_id')
                            ->options(fn () => Member::where('is_active', true)
                                ->whereDoesntHave('user')
                                ->pluck('full_name', 'id')
                                ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->live()
                            ->label('Member')
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $member = Member::find($state);

                                    if ($member) {
                                        $set('name', $member->full_name);
                                        $set('email', $member->email);
                                    }
                                }
                            })
                            ->visible(fn (string $operation): bool => $operation === 'create'),

                        TextInput::make('name')
                            ->required()
                            ->dehydrated(false)
                            ->readOnly(fn (Get $get) => filled($get('member_id'))),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique('users', 'email', ignoreRecord: true)
                            ->dehydrated(false)
                            ->readOnly(fn (Get $get) => filled($get('member_id'))),

                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn ($state) => filled($state))
                            ->default(fn () => Str::random(12))
                            ->visible(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8),

                        Toggle::make('is_active')
                            ->default(true)
                            ->inline(false)
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                    ]),
                Section::make('Roles')
                    ->components([
                        Select::make('roles')
                            ->multiple()
                            ->relationship(
                                'roles',
                                'name',
                                modifyQueryUsing: fn ($query) => $query->when(
                                    ! auth()->user()?->hasRole('super_admin'),
                                    fn ($q) => $q->where('name', '!=', 'super_admin'),
                                ),
                            )
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }
}
