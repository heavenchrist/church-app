<?php

namespace App\Filament\Pages;

use App\Enums\AttendanceType;
use App\Filament\Widgets\FollowUpStatsWidget;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\Service;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MemberFollowUp extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationLabel = 'Follow-ups';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-phone-arrow-up-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Outreach';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'follow-ups';

    protected string $view = 'filament.pages.member-follow-up';

    public static function getNavigationBadge(): ?string
    {
        $count = Member::where('needs_follow_up', true)
            ->where('is_active', true)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FollowUpStatsWidget::class,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Member::where('needs_follow_up', true)
                ->where('is_active', true)
                ->with('bibleStudyGroup'))
            ->defaultSort('follow_up_needed_since', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('bibleStudyGroup.name')
                    ->label('Bible Study Group')
                    ->sortable(),
                Tables\Columns\TextColumn::make('follow_up_needed_since')
                    ->label('Needs Attention Since')
                    ->dateTime('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('follow_up_cleared_at')
                    ->label('Last Cleared')
                    ->dateTime('M d, Y')
                    ->placeholder('Never'),
            ])
            ->filters([
                SelectFilter::make('bible_study_group')
                    ->relationship('bibleStudyGroup', 'name')
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('followUpDone')
                    ->label('Follow-up Done')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Mark follow-up as done?')
                    ->modalDescription('This will clear the follow-up flag for this member.')
                    ->action(function (Member $record) {
                        $record->update([
                            'needs_follow_up' => false,
                            'follow_up_cleared_at' => now(),
                            'follow_up_needed_since' => null,
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('markAllDone')
                    ->label('Mark All as Done')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->action(function () {
                        $count = Member::where('needs_follow_up', true)
                            ->where('is_active', true)
                            ->update([
                                'needs_follow_up' => false,
                                'follow_up_cleared_at' => now(),
                                'follow_up_needed_since' => null,
                            ]);

                        Notification::make()
                            ->title($count.' member(s) marked as done')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('scan')
                ->label('Scan for Follow-ups')
                ->icon('heroicon-o-magnifying-glass')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Scan for members needing follow-up?')
                ->modalDescription('Checks members who have missed the last 3 consecutive Sunday services.')
                ->action(function () {
                    $recentServiceIds = Service::where('service_type', '!=', 'ministry_service')
                        ->orderBy('service_date', 'desc')
                        ->take(3)
                        ->pluck('id');

                    if ($recentServiceIds->count() < 3) {
                        Notification::make()
                            ->title('Not enough services recorded to run a scan (need at least 3)')
                            ->warning()
                            ->send();

                        return;
                    }

                    $attendedIds = Attendance::whereIn('service_id', $recentServiceIds)
                        ->whereIn('attendance_type', [
                            AttendanceType::Present->value,
                            AttendanceType::Late->value,
                            AttendanceType::Excused->value,
                        ])
                        ->distinct()
                        ->pluck('member_id');

                    $now = now();

                    $count = Member::where('is_active', true)
                        ->where('needs_follow_up', false)
                        ->whereNotIn('id', $attendedIds)
                        ->update([
                            'needs_follow_up' => true,
                            'follow_up_needed_since' => $now,
                        ]);

                    if ($count > 0) {
                        Notification::make()
                            ->title($count.' member(s) flagged for follow-up')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('No new members need follow-up')
                            ->info()
                            ->send();
                    }
                }),
        ];
    }
}
