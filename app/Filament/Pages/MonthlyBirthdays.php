<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BirthdayStatsOverview;
use App\Models\Member;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MonthlyBirthdays extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationLabel = 'Monthly Birthdays';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cake';

    protected static string|\UnitEnum|null $navigationGroup = 'Membership';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'monthly-birthdays';

    protected string $view = 'filament.pages.monthly-birthdays';

    protected function getHeaderWidgets(): array
    {
        return [
            BirthdayStatsOverview::class,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Member::where('is_active', true))
            ->defaultSort('date_of_birth')
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->label('Birthday')
                    ->date('F j')
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderByRaw('DAY(date_of_birth) '.$direction)),
                Tables\Columns\TextColumn::make('age')
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('date_of_birth', $direction)),
                Tables\Columns\TextColumn::make('gender')
                    ->badge(),
                Tables\Columns\TextColumn::make('phone'),
            ])
            ->filters([
                Filter::make('filter')
                    ->schema([
                        Select::make('month')
                            ->label('Month')
                            ->options([
                                1 => 'January', 2 => 'February', 3 => 'March',
                                4 => 'April', 5 => 'May', 6 => 'June',
                                7 => 'July', 8 => 'August', 9 => 'September',
                                10 => 'October', 11 => 'November', 12 => 'December',
                            ])
                            ->default(now()->month)
                            ->live()
                            ->native(false),
                        Select::make('gender')
                            ->label('Gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ])
                            ->placeholder('All Genders')
                            ->live()
                            ->native(false),
                    ])->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['month'] ?? null, fn (Builder $q, $month) => $q->whereMonth('date_of_birth', $month))
                            ->when($data['gender'] ?? null, fn (Builder $q, $gender) => $q->where('gender', $gender));
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(2);
    }
}
