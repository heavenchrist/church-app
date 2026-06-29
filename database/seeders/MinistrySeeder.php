<?php

namespace Database\Seeders;

use App\Models\Ministry;
use Illuminate\Database\Seeder;

class MinistrySeeder extends Seeder
{
    public function run(): void
    {
        $traditional = [
            [
                'name' => 'Children Ministry',
                'slug' => 'children-ministry',
                'description' => 'Ministry for children less than 13 years',
                'color' => '#F59E0B',
                'type' => 'traditional',
                'gender' => 'both',
                'age_min' => null,
                'age_max' => 12,
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Youth Ministry',
                'slug' => 'youth-ministry',
                'description' => 'Ministry for young people aged 13-35',
                'color' => '#10B981',
                'type' => 'traditional',
                'gender' => 'both',
                'age_min' => 13,
                'age_max' => 35,
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Teens',
                'slug' => 'teens',
                'description' => 'Teens aged 13-19 (part of Youth Ministry)',
                'color' => '#6366F1',
                'type' => 'ministry_group',
                'gender' => 'both',
                'age_min' => 13,
                'age_max' => 19,
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Adult Youth',
                'slug' => 'adult-youth',
                'description' => 'Young adults aged 20-35 (part of Youth Ministry)',
                'color' => '#8B5CF6',
                'type' => 'ministry_group',
                'gender' => 'both',
                'age_min' => 20,
                'age_max' => 35,
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Women Ministry',
                'slug' => 'women-ministry',
                'description' => 'Ministry for females aged 13 and above',
                'color' => '#EC4899',
                'type' => 'traditional',
                'gender' => 'female',
                'age_min' => 13,
                'age_max' => null,
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Virtuous Ladies',
                'slug' => 'virtuous-ladies',
                'description' => 'Young women aged 13-35 (part of Women Ministry)',
                'color' => '#F472B6',
                'type' => 'ministry_group',
                'gender' => 'female',
                'age_min' => 13,
                'age_max' => 35,
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Pentecost Men Ministry (Pemem)',
                'slug' => 'pemem',
                'description' => 'Ministry for males aged 13 and above',
                'color' => '#3B82F6',
                'type' => 'traditional',
                'gender' => 'male',
                'age_min' => 13,
                'age_max' => null,
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Gentle Giants',
                'slug' => 'gentle-giants',
                'description' => 'Young men aged 13-35 (part of Pemem)',
                'color' => '#06B6D4',
                'type' => 'ministry_group',
                'gender' => 'male',
                'age_min' => 13,
                'age_max' => 35,
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Evangelism Ministry',
                'slug' => 'evangelism-ministry',
                'description' => 'Default ministry for outreach - ages 13 and above',
                'color' => '#EF4444',
                'type' => 'traditional',
                'gender' => 'both',
                'age_min' => 13,
                'age_max' => null,
                'is_default' => true,
                'is_active' => true,
            ],
        ];

        $groups = [
            [
                'name' => 'Protocols',
                'slug' => 'protocols',
                'description' => 'Protocol and hospitality team',
                'color' => '#64748B',
                'type' => 'group',
                'gender' => 'both',
                'age_min' => null,
                'age_max' => null,
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Ushers',
                'slug' => 'ushers',
                'description' => 'Ushering and welcome team',
                'color' => '#8B5CF6',
                'type' => 'group',
                'gender' => 'both',
                'age_min' => null,
                'age_max' => null,
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Choir',
                'slug' => 'choir',
                'description' => 'Choir and worship team',
                'color' => '#14B8A6',
                'type' => 'group',
                'gender' => 'both',
                'age_min' => null,
                'age_max' => null,
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Media Team',
                'slug' => 'media-team',
                'description' => 'Sound, video and media team',
                'color' => '#F97316',
                'type' => 'group',
                'gender' => 'both',
                'age_min' => null,
                'age_max' => null,
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Decoration Team',
                'slug' => 'decoration-team',
                'description' => 'Church decoration and aesthetics',
                'color' => '#EC4899',
                'type' => 'group',
                'gender' => 'both',
                'age_min' => null,
                'age_max' => null,
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        $ministries = array_merge($traditional, $groups);
        $created = [];

        foreach ($ministries as $ministry) {
            $created[$ministry['slug']] = Ministry::firstOrCreate(['slug' => $ministry['slug']], $ministry);
        }

        $parentMappings = [
            'teens' => 'youth-ministry',
            'adult-youth' => 'youth-ministry',
            'virtuous-ladies' => 'women-ministry',
            'gentle-giants' => 'pemem',
        ];

        foreach ($parentMappings as $childSlug => $parentSlug) {
            if (isset($created[$childSlug]) && isset($created[$parentSlug])) {
                $created[$childSlug]->update(['parent_id' => $created[$parentSlug]->id]);
            }
        }
    }
}
