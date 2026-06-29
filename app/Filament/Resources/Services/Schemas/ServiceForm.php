<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Enums\ServiceType;
use Filament\Forms\Components;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Service Information')
                    ->schema([
                        Components\TextInput::make('topic')->required()
                            ->unique(ignoreRecord: true),
                        Components\Select::make('service_type')
                            ->options(collect(ServiceType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()]))
                            ->searchable()
                            ->preload()
                            ->live(),
                        Components\Select::make('ministry_id')
                            ->relationship('ministry', 'name', modifyQueryUsing: fn ($query) => $query->traditional())
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get) => $get('service_type') === ServiceType::MinistryService->value)
                            ->required(fn (Get $get) => $get('service_type') === ServiceType::MinistryService->value),
                        Components\DatePicker::make('service_date')->minDate(today())->required()->native(false),
                        Components\Toggle::make('is_active')->default(true)->inline(false),
                    ]),
            ]);
    }
}
