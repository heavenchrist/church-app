<?php

namespace App\Filament\Resources\Visitors\Schemas;

use App\Enums\Gender;
use App\Enums\VisitorStatus;
use Filament\Forms\Components;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class VisitorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Visitor Info')
                    ->schema([
                        Components\TextInput::make('name')->required()->columnSpanFull(),
                        Components\TextInput::make('phone')->tel()->regex('/^0[0-9]{9}$/'),
                        Components\TextInput::make('email')->email(),
                        Components\Select::make('gender')
                            ->required()
                            ->options(Gender::class)
                            ->searchable()
                            ->preload(),
                        Components\DatePicker::make('visit_date')
                            ->default(today())->maxDate(today())->required()->native(false),
                        Components\Textarea::make('address')->columnSpanFull(),

                    ])->columns(2)->columnSpanFull(),
                Fieldset::make('Invitation')
                    ->schema([
                        Components\Toggle::make('invited_by_member')
                            ->label('Invited by Member')
                            ->inline(false)
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('invited_by_name', null);
                                } else {
                                    $set('invited_by_member_id', null);
                                }
                            }),
                        Components\Select::make('invited_by_member_id')
                            ->relationship('invitedByMember', 'full_name')
                            ->preload()
                            ->columnSpanFull()
                            ->searchable()
                            ->visible(fn (Get $get): bool => $get('invited_by_member')),
                        Components\TextInput::make('invited_by_name')
                            ->label('Invited by Name')
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => ! $get('invited_by_member')),
                        Components\Textarea::make('how_heard_about_church')->columnSpanFull(),
                    ])->columnSpanFull(),
                Fieldset::make('Follow-up')
                    ->schema([
                        Components\Toggle::make('is_followed_up')->inline(false),
                        Components\DatePicker::make('followed_up_at')->native(false),
                        Components\Select::make('status')
                            ->options(collect(VisitorStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()]))
                            ->searchable()
                            ->required()
                            ->preload(),
                        Components\Textarea::make('remarks')->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
