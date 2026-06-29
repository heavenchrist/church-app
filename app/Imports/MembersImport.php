<?php

namespace App\Imports;

use App\Models\BibleStudyGroup;
use App\Models\Member;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MembersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $bibleStudyGroup = BibleStudyGroup::where('name', 'like', '%'.($row['bible_study_group'] ?? '').'%')->first();

        return new Member([
            'member_id' => Member::generateMemberId(),
            'first_name' => $row['first_name'] ?? '',
            'middle_name' => $row['middle_name'] ?? null,
            'last_name' => $row['last_name'] ?? '',
            'date_of_birth' => isset($row['date_of_birth']) ? Carbon::parse($row['date_of_birth'])->format('Y-m-d') : now()->subYears(25)->format('Y-m-d'),
            'gender' => strtolower($row['gender'] ?? 'male'),
            'phone' => $row['phone'] ?? null,
            'email' => $row['email'] ?? null,
            'marital_status' => $row['marital_status'] ?? 'single',
            'residential_address' => $row['residential_address'] ?? null,
            'bible_study_group_id' => $bibleStudyGroup?->id ?? BibleStudyGroup::first()?->id,
            'classification' => $row['classification'] ?? 'regular',
            'status' => $row['status'] ?? 'member',
            'water_baptism_date' => isset($row['water_baptism_date']) ? Carbon::parse($row['water_baptism_date'])->format('Y-m-d') : null,
            'holy_spirit_baptism_date' => isset($row['holy_spirit_baptism_date']) ? Carbon::parse($row['holy_spirit_baptism_date'])->format('Y-m-d') : null,
            'is_active' => true,
        ]);
    }
}
