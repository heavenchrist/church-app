<?php

namespace App\Console\Commands;

use App\Enums\AttendanceType;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\Service;
use Illuminate\Console\Command;

class ScanFollowUps extends Command
{
    protected $signature = 'app:scan-follow-ups';

    protected $description = 'Scan members who missed 3 consecutive Sunday services and flag them for follow-up';

    public function handle(): int
    {
        $recentServiceIds = Service::where('service_type', '!=', 'ministry_service')
            ->orderBy('service_date', 'desc')
            ->take(3)
            ->pluck('id');

        if ($recentServiceIds->count() < 3) {
            $this->warn('Not enough services recorded (need at least 3).');

            return self::SUCCESS;
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

        $this->info("{$count} member(s) flagged for follow-up.");

        return self::SUCCESS;
    }
}
