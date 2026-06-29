<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Enums\Classification;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\MemberStatus;
use App\Enums\Occupation;
use App\Filament\Resources\Members\Pages\CreateMember;
use App\Models\BibleStudyGroup;
use App\Models\Ministry;
use Carbon\Carbon;
use Filament\Forms\Components;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\GridDirection;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->components([
                        Components\FileUpload::make('profile_photo')
                            ->image()
                            ->imageEditor()
                            ->imageAspectRatio('1:1')
                            ->automaticallyCropImagesToAspectRatio()
                            ->automaticallyResizeImagesToHeight(300)
                            ->automaticallyResizeImagesToWidth(300)
                            ->directory('members/photos')
                            ->avatar()
                            ->alignCenter(),
                        Grid::make(2)
                            ->schema([
                                Components\TextInput::make('full_name')->required()->columnSpanFull(),
                                Components\DatePicker::make('date_of_birth')->required()->native(false)->maxDate(Carbon::now()),
                                Components\Select::make('gender')
                                    ->options(Gender::memberOptions())
                                    // ->options(collect(Gender::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()]))
                                    ->preload()
                                    ->required()
                                    ->searchable()
                                    ->live(),
                                Components\Select::make('marital_status')
                                    ->options(collect(MaritalStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()]))
                                    ->preload()
                                    ->required()
                                    ->searchable(),
                                Components\Select::make('occupation')
                                    ->options(Occupation::class)
                                    ->preload()
                                    ->searchable()
                                    ->native(false),
                            ]),
                    ]),
                Section::make('Contact Information')
                    ->components([
                        Grid::make(2)
                            ->schema([
                                Components\TextInput::make('phone')->tel()->regex('/^0[0-9]{9}$/'),
                                Components\TextInput::make('email')->email(),
                                Components\Textarea::make('residential_address')->rows(2)->columnSpanFull(),
                                Components\TextInput::make('gps_address'),
                                Components\TextInput::make('emergency_contact_name'),
                                Components\TextInput::make('emergency_contact_phone')->tel()->regex('/^0[0-9]{9}$/'),
                                Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
                            ]),
                    ]),
                Section::make('Church Information')
                    ->components([
                        Components\Toggle::make('auto_assign_bible_study_group')
                            ->label('Auto-assign Bible Study Group')
                            ->default(fn ($livewire) => $livewire instanceof CreateMember)
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(fn ($state, Set $set) => $state
                                ? $set('bible_study_group_id', BibleStudyGroup::getNextAssignableGroup()?->id)
                                : null
                            ),
                        Components\Select::make('status')
                            ->options(function ($livewire) {
                                $cases = $livewire instanceof CreateMember
                                    ? MemberStatus::createOptions()
                                    : MemberStatus::updateOptions();

                                return collect($cases)
                                    ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()]);
                            })
                            ->preload()
                            ->searchable()
                            ->live(),
                        Grid::make(2)
                            ->schema([
                                Components\DatePicker::make('date_joined')->required()->native(false)->maxDate(Carbon::now()),
                                Components\DatePicker::make('water_baptism_date')->native(false)->maxDate(Carbon::now()),
                                Components\DatePicker::make('holy_spirit_baptism_date')->native(false)->maxDate(Carbon::now()),
                                Components\Select::make('classification')
                                    ->required()
                                    ->options(function (Get $get) {
                                        $gender = $get('gender');

                                        if (empty($gender)) {
                                            return [];
                                        }

                                        $maleOptions = collect(Classification::cases())
                                            ->filter(fn ($case) => $case->value !== 'deaconess')
                                            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
                                            ->toArray();

                                        $femaleOptions = collect(Classification::cases())
                                            ->filter(fn ($case) => $case->value !== 'elder' && $case->value !== 'deacon')
                                            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
                                            ->toArray();

                                        if ($gender === 'male') {
                                            return $maleOptions;
                                        } elseif ($gender === 'female') {
                                            return $femaleOptions;
                                        }

                                        return [];
                                    })
                                    ->preload()
                                    ->searchable()
                                    ->disabled(fn (Get $get) => empty($get('gender'))),
                                Components\Select::make('bible_study_group_id')
                                    ->relationship('bibleStudyGroup', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->dehydrated()
                                    ->columnSpanFull()
                                    ->default(fn () => BibleStudyGroup::getNextAssignableGroup()?->id)
                                    ->hidden(fn (Get $get) => $get('status') === 'visitor')
                                    ->disabled(fn (Get $get) => $get('auto_assign_bible_study_group')),
                            ]),
                        Components\Select::make('assigned_to_member_id')
                            ->label('Assigned To')
                            ->relationship('assignedTo', 'full_name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('status') === 'convert')
                            ->required(fn (Get $get): bool => $get('status') === 'convert'),
                    ]),
                Section::make('Ministries')
                    ->schema([
                        Fieldset::make('Active Ministries')
                            ->schema([
                                Components\CheckboxList::make('traditional_ministries')->hiddenLabel()
                                    ->relationship('traditional_ministries', 'name', modifyQueryUsing: fn ($query) => $query->traditional())
                                    ->live()
                                    ->required()
                                    ->options(function (Get $get) {
                                        $selectedIds = collect($get('traditional_ministries') ?? []);
                                        $selected = Ministry::whereIn('id', $selectedIds)->get();

                                        $excludeIds = [];
                                        foreach ($selected as $m) {
                                            $excludeIds = array_merge($excludeIds, $m->getDescendantIds());
                                        }

                                        $gender = $get('gender');

                                        $available = Ministry::traditional()
                                            ->when($gender, fn ($query) => $query->where(function ($q) use ($gender) {
                                                $q->where('gender', 'both')->orWhere('gender', $gender);
                                            }))
                                            ->whereNotIn('id', $excludeIds)
                                            ->pluck('name', 'id');

                                        if ($selectedIds->isNotEmpty()) {
                                            $available = $available->union(
                                                Ministry::whereIn('id', $selectedIds)->pluck('name', 'id')
                                            );
                                        }

                                        return $available;
                                    })->columns(2)->gridDirection(GridDirection::Row)->columnSpanFull(),
                            ]),
                        Fieldset::make('Active Groups')
                            ->schema([
                                Components\CheckboxList::make('group_ministries')->hiddenLabel()
                                    ->relationship('group_ministries', 'name', modifyQueryUsing: fn ($query) => $query->groups())
                                    ->live()
                                    ->options(function (Get $get) {
                                        $gender = $get('gender');
                                        $selectedIds = collect($get('group_ministries') ?? []);

                                        $available = Ministry::groups()
                                            ->where('is_assignable', true)
                                            ->when($gender, fn ($query) => $query->where(function ($q) use ($gender) {
                                                $q->where('gender', 'both')->orWhere('gender', $gender);
                                            }))
                                            ->pluck('name', 'id');

                                        if ($selectedIds->isNotEmpty()) {
                                            $available = $available->union(
                                                Ministry::whereIn('id', $selectedIds)->pluck('name', 'id')
                                            );
                                        }

                                        return $available;
                                    })->columns(2)->gridDirection(GridDirection::Row)->columnSpanFull(),
                            ]),
                    ])->columnSpanFull()->columns(2),
            ]);
    }
}
