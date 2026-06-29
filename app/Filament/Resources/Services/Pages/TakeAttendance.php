<?php

namespace App\Filament\Resources\Services\Pages;

use App\Enums\AttendanceType;
use App\Enums\Gender;
use App\Enums\MemberStatus;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Attendance;
use App\Models\Member;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class TakeAttendance extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = ServiceResource::class;

    protected string $view = 'filament.resources.services.pages.take-attendance';

    public $record;

    private function getAttendanceMap(): array
    {
        $memberIds = Member::where('is_active', true)->pluck('id');

        return Attendance::where('service_id', $this->record->id)
            ->whereIn('member_id', $memberIds)
            ->pluck('attendance_type', 'member_id')
            ->toArray();
    }

    public function mount(int|string $record): void
    {
        $this->record = static::getResource()::resolveRecordRouteBinding($record);

        abort_unless($this->record, 404);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Member::where('is_active', true))
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
                SelectFilter::make('status')
                    ->options(MemberStatus::class),
                SelectFilter::make('gender')
                    ->options(Gender::class),
                SelectFilter::make('bible_study_group')
                    ->relationship('bibleStudyGroup', 'name')
                    ->preload(),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        $members = Member::where('is_active', true)
                            ->orderBy('full_name')
                            ->get();

                        $attendances = Attendance::where('service_id', $this->record->id)
                            ->whereIn('member_id', $members->pluck('id'))
                            ->get()
                            ->keyBy('member_id');

                        $data = $members->map(fn ($member) => [
                            'full_name' => $member->full_name,
                            'gender' => $member->gender?->getLabel() ?? '',
                            'phone' => $member->phone,
                            'attendance' => $attendances->get($member->id)?->attendance_type instanceof AttendanceType
                                ? $attendances->get($member->id)->attendance_type->getLabel()
                                : 'Not Marked',
                        ])->toArray();

                        return Excel::download(new class($data) implements FromArray, WithHeadings
                        {
                            public function __construct(private array $data) {}

                            public function array(): array
                            {
                                return $this->data;
                            }

                            public function headings(): array
                            {
                                return ['Full Name', 'Gender', 'Phone', 'Attendance'];
                            }
                        }, 'attendance-'.now()->format('Y-m-d').'.xlsx');
                    }),
                Action::make('markAbsent')
                    ->label('Mark Unmarked as Absent')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Mark all unmarked members as Absent?')
                    ->modalDescription('Members without an attendance record for this service will be marked as Absent.')
                    ->action(function () {
                        $memberIds = Member::where('is_active', true)->pluck('id');

                        $markedIds = Attendance::where('service_id', $this->record->id)
                            ->whereIn('member_id', $memberIds)
                            ->pluck('member_id');

                        $unmarkedIds = $memberIds->diff($markedIds);

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
                            'service_id' => $this->record->id,
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
                    ->form([
                        Select::make('attendance_type')
                            ->label('Attendance')
                            ->options(AttendanceType::class)
                            ->default(fn ($record) => $this->getAttendanceMap()[$record->id] ?? 'not_marked')
                            ->native(false),
                    ])
                    ->modalWidth(Width::Small)
                    ->closeModalByClickingAway(false)
                    ->action(function (array $data, $record) {
                        $attendanceType = $data['attendance_type'] instanceof AttendanceType
                            ? $data['attendance_type']->value
                            : $data['attendance_type'];

                        Attendance::updateOrCreate(
                            ['service_id' => $this->record->id, 'member_id' => $record->id],
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
