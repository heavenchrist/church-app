<?php

namespace App\Filament\Resources\Services\RelationManagers;

use App\Enums\AttendanceType;
use App\Enums\Gender;
use App\Enums\MemberStatus;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static ?string $title = 'Members';

    protected static ?string $recordTitleAttribute = 'member.full_name';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(' ')
            ->columns([
                Tables\Columns\ImageColumn::make('member.profile_photo')->label('Photo')->circular()->size(40),
                Tables\Columns\TextColumn::make('member.full_name')->label('Name')->searchable(),
                Tables\Columns\TextColumn::make('member.gender')->label('Gender')
                    ->formatStateUsing(fn ($state) => $state?->getLabel())
                    ->badge(),
                Tables\Columns\TextColumn::make('member.phone')->label('Phone'),
                Tables\Columns\TextColumn::make('attendance_type')->badge(),
            ])
            ->filters([
                SelectFilter::make('attendance_type')
                    ->label('Attendance Type')
                    ->options(AttendanceType::class),
                SelectFilter::make('gender')
                    ->options(Gender::class)
                    ->query(fn (Builder $query, array $data) => $query->whereHas('member', fn (Builder $q) => $q->where('gender', $data['value']))
                    ),
                SelectFilter::make('status')
                    ->options(MemberStatus::class)
                    ->query(fn (Builder $query, array $data) => $query->whereHas('member', fn (Builder $q) => $q->where('status', $data['value']))
                    ),
                SelectFilter::make('bible_study_group')
                    ->label('Bible Study Group')
                    ->relationship('member.bibleStudyGroup', 'name')
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        $attendances = $this->getFilteredTableQuery()
                            ->with('member')
                            ->get();

                        $data = $attendances->map(fn ($a) => [
                            'full_name' => $a->member?->full_name ?? '',
                            'gender' => $a->member?->gender?->getLabel() ?? '',
                            'phone' => $a->member?->phone ?? '',
                            'attendance' => AttendanceType::tryFrom($a->attendance_type)?->getLabel() ?? $a->attendance_type,
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
                        }, 'service-attendance-'.now()->format('Y-m-d').'.xlsx');
                    }),
            ]);
    }
}
