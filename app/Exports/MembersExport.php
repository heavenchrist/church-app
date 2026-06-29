<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MembersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Member::with(['bibleStudyGroup', 'ministries'])
            ->get()
            ->map(function ($member) {
                return [
                    'member_id' => $member->member_id,
                    'first_name' => $member->first_name,
                    'middle_name' => $member->middle_name,
                    'last_name' => $member->last_name,
                    'gender' => $member->gender,
                    'date_of_birth' => $member->date_of_birth->format('Y-m-d'),
                    'age' => $member->age,
                    'phone' => $member->phone,
                    'email' => $member->email,
                    'marital_status' => $member->marital_status,
                    'residential_address' => $member->residential_address,
                    'bible_study_group' => $member->bibleStudyGroup?->name,
                    'classification' => $member->classification,
                    'status' => $member->status,
                    'water_baptism_date' => $member->water_baptism_date?->format('Y-m-d'),
                    'holy_spirit_baptism_date' => $member->holy_spirit_baptism_date?->format('Y-m-d'),
                    'is_active' => $member->is_active ? 'Yes' : 'No',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Member ID',
            'First Name',
            'Middle Name',
            'Last Name',
            'Gender',
            'Date of Birth',
            'Age',
            'Phone',
            'Email',
            'Marital Status',
            'Residential Address',
            'Bible Study Group',
            'Classification',
            'Status',
            'Water Baptism Date',
            'Holy Spirit Baptism Date',
            'Is Active',
        ];
    }
}
