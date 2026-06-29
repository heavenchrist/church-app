<?php

namespace App\Filament\Pages;

use App\Enums\AttendanceType;
use App\Models\Attendance;
use App\Models\BibleStudyGroup;
use App\Models\Member;
use App\Models\Service;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class GroupAttendance extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Take Attendance';

    protected static string|\UnitEnum|null $navigationGroup = 'Services & Attendance';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'group-attendance';

    protected string $view = 'filament.pages.group-attendance';

    private function getAttendanceMap(): array
    {
        $serviceId = $this->tableFilters['selectFilters']['serviceId'] ?? null;
        $bibleStudyGroupId = $this->tableFilters['selectFilters']['bibleStudyGroupId'] ?? null;

        if (! $serviceId || ! $bibleStudyGroupId) {
            return [];
        }

        $memberIds = Member::where('is_active', true)
            ->where('bible_study_group_id', $bibleStudyGroupId)
            ->pluck('id');

        return Attendance::where('service_id', $serviceId)
            ->whereIn('member_id', $memberIds)
            ->pluck('attendance_type', 'member_id')
            ->toArray();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ($this->tableFilters['selectFilters']['serviceId'] ?? null) &&
                ($this->tableFilters['selectFilters']['bibleStudyGroupId'] ?? null)
                    ? Member::where('is_active', true)->where('bible_study_group_id', $this->tableFilters['selectFilters']['bibleStudyGroupId'])
                    : Member::whereRaw('0 = 1')
            )
            ->recordUrl(' ')
            ->defaultSort('full_name')
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('attendance_type')
                    ->label('Attendance')
                    ->badge()
                    ->color(fn (AttendanceType|string|null $state) => match (true) {
                        $state instanceof AttendanceType => $state->getColor(),
                        $state === 'not_marked' => 'gray',
                        default => AttendanceType::tryFrom($state ?? '')?->getColor(),
                    })
                    ->getStateUsing(fn ($record) => $this->getAttendanceMap()[$record->id] ?? 'not_marked')
                    ->formatStateUsing(fn (AttendanceType|string|null $state) => match (true) {
                        $state instanceof AttendanceType => $state->getLabel(),
                        $state === 'not_marked' => 'Not Marked',
                        default => AttendanceType::tryFrom($state ?? '')?->getLabel() ?? ucfirst($state ?? ''),
                    }),
            ])
            ->filters([
                Filter::make('selectFilters')
                    ->schema([
                        Select::make('serviceId')
                            ->label('Service')
                            ->options(fn (): array => Service::where('service_date', '>=', now()->startOfDay())
                                ->where('service_type', '!=', 'ministry_service')
                                ->orderBy('service_date')
                                ->get()
                                ->mapWithKeys(fn (Service $service): array => [$service->id => "{$service->topic} ({$service->service_date->format('M d, Y')})"])
                                ->toArray())
                            ->required()
                            ->live()
                            ->native(false),
                        Select::make('bibleStudyGroupId')
                            ->label('Bible Study Group')
                            ->options(fn (): array => BibleStudyGroup::orderBy('name')->pluck('name', 'id')->toArray())
                            ->required()
                            ->live()
                            ->native(false),
                    ])->columnSpanFull()->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->where('bible_study_group_id', $data['bibleStudyGroupId']);
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->toolbarActions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalWidth(Width::Small)
                    ->closeModalByClickingAway(false)
                    ->schema([
                        Select::make('service_id')
                            ->label('Service')
                            ->options(fn (): array => Service::where('service_type', '!=', 'ministry_service')
                                ->orderBy('service_date', 'desc')
                                ->get()
                                ->mapWithKeys(fn (Service $service): array => [$service->id => "{$service->topic} ({$service->service_date->format('M d, Y')})"])
                                ->toArray())
                            ->required()
                            ->native(false),
                        Select::make('bible_study_group_id')
                            ->label('Bible Study Group')
                            ->options(fn (): array => BibleStudyGroup::orderBy('name')->pluck('name', 'id')->toArray())
                            ->required()
                            ->native(false),
                        Select::make('attendance_type')
                            ->label('Attendance Type')
                            ->options(AttendanceType::class)
                            ->placeholder('All')
                            ->native(false),
                    ])
                    ->action(function (array $data) {
                        $serviceId = $data['service_id'];
                        $bibleStudyGroupId = $data['bible_study_group_id'];

                        $rawFilter = $data['attendance_type'] ?? null;
                        $attendanceTypeFilter = match (true) {
                            $rawFilter instanceof AttendanceType => $rawFilter->value,
                            is_string($rawFilter) => $rawFilter,
                            default => null,
                        };

                        $members = Member::where('is_active', true)
                            ->where('bible_study_group_id', $bibleStudyGroupId)
                            ->orderBy('full_name')
                            ->get();

                        $service = Service::find($serviceId);

                        $attendances = Attendance::where('service_id', $serviceId)
                            ->whereIn('member_id', $members->pluck('id'))
                            ->get()
                            ->keyBy('member_id');

                        $exportData = $members
                            ->filter(fn ($member) => ! $attendanceTypeFilter
                                || ($attendances->get($member->id)?->attendance_type ?? 'not_marked') === $attendanceTypeFilter)
                            ->map(fn ($member) => [
                                'full_name' => $member->full_name,
                                'gender' => $member->gender?->getLabel() ?? '',
                                'phone' => $member->phone,
                                'bible_study_group' => $member->bibleStudyGroup?->name ?? '',
                                'service' => $service?->topic ?? '',
                                'service_date' => $service?->service_date?->format('M d, Y') ?? '',
                                'attendance' => $attendances->get($member->id)?->attendance_type instanceof AttendanceType
                                    ? $attendances->get($member->id)->attendance_type->getLabel()
                                    : 'Not Marked',
                            ])->toArray();

                        if (empty($exportData)) {
                            Notification::make()
                                ->title('No records match the selected criteria')
                                ->warning()
                                ->send();

                            return;
                        }

                        return Excel::download(new class($exportData) implements FromArray, WithHeadings
                        {
                            public function __construct(private array $data) {}

                            public function array(): array
                            {
                                return $this->data;
                            }

                            public function headings(): array
                            {
                                return ['Full Name', 'Gender', 'Phone', 'Bible Study Group', 'Service', 'Service Date', 'Attendance'];
                            }
                        }, 'group-attendance-'.now()->format('Y-m-d').'.xlsx');
                    }),
                Action::make('markAbsent')
                    ->label('Mark Unmarked as Absent')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Mark all unmarked members as Absent?')
                    ->modalDescription('Members without an attendance record for this service will be marked as Absent.')
                    ->action(function () {
                        $serviceId = $this->tableFilters['selectFilters']['serviceId'] ?? null;
                        $bibleStudyGroupId = $this->tableFilters['selectFilters']['bibleStudyGroupId'] ?? null;

                        if (! $serviceId || ! $bibleStudyGroupId) {
                            Notification::make()
                                ->title('Please select a service and group first')
                                ->warning()
                                ->send();

                            return;
                        }

                        $groupMemberIds = Member::where('is_active', true)
                            ->where('bible_study_group_id', $bibleStudyGroupId)
                            ->pluck('id');

                        $markedIds = Attendance::where('service_id', $serviceId)
                            ->whereIn('member_id', $groupMemberIds)
                            ->pluck('member_id');

                        $unmarkedIds = $groupMemberIds->diff($markedIds);

                        if ($unmarkedIds->isEmpty()) {
                            Notification::make()
                                ->title('All members already have attendance records')
                                ->info()
                                ->send();

                            return;
                        }

                        $now = now();
                        $userId = auth()->id();

                        $records = $unmarkedIds->map(fn ($id) => [
                            'service_id' => $serviceId,
                            'member_id' => $id,
                            'attendance_type' => AttendanceType::Absent->value,
                            'recorded_by' => $userId,
                            'recorded_at' => $now,
                        ])->toArray();

                        Attendance::insert($records);

                        Notification::make()
                            ->title(count($unmarkedIds).' member(s) marked as Absent')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('setAttendance')
                    ->label('Set Attendance')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Select::make('attendance_type')
                            ->label('Attendance')
                            ->options(AttendanceType::class)
                            ->default(fn ($record) => $this->getAttendanceMap()[$record->id] ?? 'not_marked')
                            ->native(false),
                    ])
                    ->modalWidth(Width::Small)
                    ->closeModalByClickingAway(false)
                    ->action(function (array $data, $record) {
                        $serviceId = $this->tableFilters['selectFilters']['serviceId'] ?? null;
                        if (! $serviceId) {
                            return;
                        }

                        $attendanceType = $data['attendance_type'] instanceof AttendanceType
                            ? $data['attendance_type']->value
                            : $data['attendance_type'];

                        Attendance::updateOrCreate(
                            ['service_id' => $serviceId, 'member_id' => $record->id],
                            [
                                'attendance_type' => $attendanceType,
                                'recorded_by' => auth()->id(),
                                'recorded_at' => now(),
                            ]
                        );
                    }),
            ]);
    }
}
