<?php

namespace App\Filament\Pages;

use App\Enums\AttendanceType;
use App\Enums\ServiceType;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\Service;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Schemas\Components\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class AttendanceReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Attendance Report';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'attendance-report';

    protected string $view = 'filament.pages.attendance-report';

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public ?string $serviceType = null;

    public array $reportData = [];

    public function mount(): void
    {
        $this->form->fill([
            'fromDate' => now()->startOfMonth()->format('Y-m-d'),
            'toDate' => now()->format('Y-m-d'),
            'serviceType' => null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Report Filters')
                    ->compact()
                    ->columns(4)
                    ->schema([
                        DatePicker::make('fromDate')
                            ->label('From Date')
                            ->native(false)
                            ->required(),
                        DatePicker::make('toDate')
                            ->label('To Date')
                            ->native(false)
                            ->required(),
                        Select::make('serviceType')
                            ->label('Service Type')
                            ->placeholder('All Service Types')
                            ->options(ServiceType::class)
                            ->native(false),
                        Actions::make([
                            Action::make('generate')
                                ->label('Generate Report')
                                ->icon('heroicon-o-arrow-path')
                                ->color('primary')
                                ->action('generate'),
                        ])->columnSpan(1)->alignCenter(),
                    ]),
            ]);
    }

    public function generate(): void
    {
        $from = $this->fromDate ? Carbon::parse($this->fromDate)->startOfDay() : now()->startOfMonth();
        $to = $this->toDate ? Carbon::parse($this->toDate)->endOfDay() : now();

        $servicesQuery = Service::whereBetween('service_date', [$from, $to])
            ->when($this->serviceType, fn ($q) => $q->where('service_type', $this->serviceType))
            ->orderBy('service_date');

        $services = $servicesQuery->get();

        if ($services->isEmpty()) {
            $this->reportData = ['empty' => true];

            return;
        }

        $serviceIds = $services->pluck('id');

        $attendances = Attendance::whereIn('service_id', $serviceIds)
            ->with('member')
            ->get();

        $grandTotalPresent = 0;
        $grandTotalAbsent = 0;
        $perService = [];

        foreach ($services as $service) {
            $sa = $attendances->where('service_id', $service->id);
            $present = $sa->where('attendance_type', AttendanceType::Present)->count();
            $late = $sa->where('attendance_type', AttendanceType::Late)->count();
            $absent = $sa->where('attendance_type', AttendanceType::Absent)->count();
            $excused = $sa->where('attendance_type', AttendanceType::Excused)->count();

            $grandTotalPresent += $present;
            $grandTotalAbsent += $absent;

            $perService[] = [
                'title' => $service->topic ?? $service->service_type?->getLabel() ?? 'Service',
                'date' => $service->service_date->format('M d, Y'),
                'type' => $service->service_type?->getLabel() ?? '',
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'excused' => $excused,
                'total' => $present + $late + $absent + $excused,
            ];
        }

        $totalPresent = $attendances->where('attendance_type', AttendanceType::Present)->count();
        $totalAbsent = $attendances->where('attendance_type', AttendanceType::Absent)->count();
        $totalLate = $attendances->where('attendance_type', AttendanceType::Late)->count();
        $totalExcused = $attendances->where('attendance_type', AttendanceType::Excused)->count();

        $presentMemberIds = $attendances->where('attendance_type', AttendanceType::Present)->pluck('member_id')->unique();
        $presentMembers = Member::whereIn('id', $presentMemberIds)->get();

        $ageRanges = ['children' => 0, 'teens' => 0, 'young_adult' => 0, 'adult' => 0];
        foreach ($presentMembers as $member) {
            $group = $member->age_group;
            $ageRanges[$group] = ($ageRanges[$group] ?? 0) + 1;
        }

        $genderDist = [];
        foreach ($presentMembers as $member) {
            $g = $member->gender?->getLabel() ?? 'Unknown';
            $genderDist[$g] = ($genderDist[$g] ?? 0) + 1;
        }

        $maritalDist = [];
        foreach ($presentMembers as $member) {
            $m = $member->marital_status?->getLabel() ?? 'Unknown';
            $maritalDist[$m] = ($maritalDist[$m] ?? 0) + 1;
        }

        $servicesByType = $services->groupBy(fn ($s) => $s->service_type?->getLabel() ?? 'Other')
            ->map(fn ($group) => $group->count())
            ->toArray();

        $this->reportData = [
            'empty' => false,
            'total_services' => $services->count(),
            'total_present' => $totalPresent,
            'total_absent' => $totalAbsent,
            'total_late' => $totalLate,
            'total_excused' => $totalExcused,
            'average_attendance' => $services->count() > 0 ? round($grandTotalPresent / $services->count(), 1) : 0,
            'total_unique_attendees' => $presentMembers->count(),
            'services' => $perService,
            'age_ranges' => $ageRanges,
            'gender_distribution' => $genderDist,
            'marital_distribution' => $maritalDist,
            'services_by_type' => $servicesByType,
        ];
    }
}
