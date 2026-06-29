<?php

namespace App\Filament\Resources\Ministries\Schemas;

use App\Enums\Gender;
use App\Enums\MinistryType;
use App\Models\Ministry;
use Filament\Forms\Components;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\GridDirection;
use Illuminate\Support\Str;

class MinistryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ministry Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Components\TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if (blank($get('slug'))) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),
                                Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->regex('/^[a-z0-9\-]+$/')
                                    ->helperText('Auto-generated from name.')
                                    ->validationMessages([
                                        'regex' => 'Slug can only contain lowercase letters, numbers, and hyphens.',
                                    ])
                                    ->dehydrateStateUsing(fn ($state) => Str::slug($state)),
                            ]),
                        Components\Textarea::make('description'),
                        Grid::make(2)
                            ->schema([
                                Components\Radio::make('type')
                                    ->enum(MinistryType::class)
                                    ->options(MinistryType::class)
                                    ->required()
                                    ->columnSpanFull()
                                    ->columns(3)
                                    ->gridDirection(GridDirection::Row)
                                    ->live(),
                                Components\Radio::make('parent_id')
                                    ->label('Parent Ministry')
                                    ->options(fn () => Ministry::where('type', MinistryType::Traditional->value)
                                        ->pluck('name', 'id')
                                    )
                                    ->columnSpanFull()
                                    ->columns(3)
                                    ->gridDirection(GridDirection::Row)
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $set('gender', null);
                                    })->columnSpanFull()
                                    ->visible(fn (Get $get) => in_array($get('type'), [MinistryType::MinistryGroup]))
                                    ->required(fn (Get $get) => in_array($get('type'), [MinistryType::MinistryGroup])),
                                Components\Radio::make('gender')
                                    ->enum(Gender::class)
                                    ->required()
                                    ->live()
                                    ->columnSpanFull()
                                    ->columns(3)
                                    ->gridDirection(GridDirection::Row)
                                    ->options(function (Get $get) {
                                        $parentId = $get('parent_id');
                                        if (! $parentId) {
                                            return Gender::class;
                                        }
                                        $parent = Ministry::find($parentId);
                                        if (! $parent || ! $parent->gender) {
                                            return Gender::class;
                                        }
                                        $allowedGenders = Ministry::getAllowedGenders($parent->gender);

                                        return array_combine(
                                            array_map(fn ($g) => $g->value, $allowedGenders),
                                            array_map(fn ($g) => $g->getLabel(), $allowedGenders)
                                        );
                                    }),
                            ]),
                        Grid::make(3)
                            ->schema([
                                Components\TextInput::make('age_min')
                                    ->label('Minimum Age')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->live()
                                    ->hidden(fn (Get $get) => in_array($get('type'), [MinistryType::Group]))
                                    ->rules([
                                        function (Get $get) {
                                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                $parentId = $get('parent_id');
                                                if (! $parentId) {
                                                    return;
                                                }
                                                $parent = Ministry::find($parentId);
                                                if (! $parent) {
                                                    return;
                                                }
                                                if ($parent->age_min !== null && $value < $parent->age_min) {
                                                    $fail("Minimum age must be at least {$parent->age_min}");
                                                }
                                                if ($parent->age_max !== null && $value > $parent->age_max) {
                                                    $fail("Minimum age cannot exceed parent maximum age of {$parent->age_max}");
                                                }
                                            };
                                        },
                                    ]),
                                Components\TextInput::make('age_max')
                                    ->label('Maximum Age')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->live()
                                    ->hidden(fn (Get $get) => in_array($get('type'), [MinistryType::Group]))
                                    ->rules([
                                        function (Get $get) {
                                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                $parentId = $get('parent_id');
                                                if (! $parentId) {
                                                    return;
                                                }
                                                $parent = Ministry::find($parentId);
                                                if (! $parent) {
                                                    return;
                                                }
                                                if ($parent->age_max !== null && $value > $parent->age_max) {
                                                    $fail("Maximum age cannot exceed parent maximum age of {$parent->age_max}");
                                                }
                                                if ($parent->age_min !== null && $value < $parent->age_min) {
                                                    $fail("Maximum age must be at least {$parent->age_min}");
                                                }
                                                $minAge = $get('age_min');
                                                if ($minAge !== null && $value < $minAge) {
                                                    $fail('Maximum age must be greater than or equal to minimum age');
                                                }
                                            };
                                        },
                                    ]),
                                Components\ColorPicker::make('color')->required(),
                            ]),
                        Grid::make(3)
                            ->schema([
                                Components\Toggle::make('is_active')->default(true)->inline(false),
                                Components\Toggle::make('is_assignable')
                                    ->inline(false)
                                    ->visible(fn (Get $get) => in_array($get('type'), ['group', MinistryType::Group, MinistryType::MinistryGroup])),
                                Components\Toggle::make('is_default')->inline(false),
                            ]),
                    ]),
            ]);
    }
}
