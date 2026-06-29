<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BibleStudyGroup;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class BibleStudyGroupPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BibleStudyGroup');
    }

    public function view(AuthUser $authUser, BibleStudyGroup $bibleStudyGroup): bool
    {
        return $authUser->can('View:BibleStudyGroup');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BibleStudyGroup');
    }

    public function update(AuthUser $authUser, BibleStudyGroup $bibleStudyGroup): bool
    {
        return $authUser->can('Update:BibleStudyGroup');
    }

    public function delete(AuthUser $authUser, BibleStudyGroup $bibleStudyGroup): bool
    {
        return $authUser->can('Delete:BibleStudyGroup');
    }

    public function restore(AuthUser $authUser, BibleStudyGroup $bibleStudyGroup): bool
    {
        return $authUser->can('Restore:BibleStudyGroup');
    }

    public function forceDelete(AuthUser $authUser, BibleStudyGroup $bibleStudyGroup): bool
    {
        return $authUser->can('ForceDelete:BibleStudyGroup');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BibleStudyGroup');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BibleStudyGroup');
    }

    public function replicate(AuthUser $authUser, BibleStudyGroup $bibleStudyGroup): bool
    {
        return $authUser->can('Replicate:BibleStudyGroup');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BibleStudyGroup');
    }
}
