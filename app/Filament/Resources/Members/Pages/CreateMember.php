<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Models\Member;
use Filament\Resources\Pages\CreateRecord;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    protected function afterCreate(): void
    {
        $member = $this->record;
        if (empty($member->member_id)) {
            $member->member_id = Member::generateMemberId();
            $member->save();
        }
    }
}
