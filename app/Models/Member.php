<?php

namespace App\Models;

use App\Enums\Classification;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\MemberStatus;
use App\Enums\Occupation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'bible_study_group_id',
        'member_id',
        'full_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'residential_address',
        'gps_address',
        'marital_status',
        'occupation',
        'water_baptism_date',
        'holy_spirit_baptism_date',
        'date_joined',
        'classification',
        'status',
        'assigned_to_member_id',
        'profile_photo',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
        'is_active',
        'needs_follow_up',
        'follow_up_cleared_at',
        'follow_up_needed_since',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'water_baptism_date' => 'date',
        'holy_spirit_baptism_date' => 'date',
        'date_joined' => 'date',
        'is_active' => 'boolean',
        'needs_follow_up' => 'boolean',
        'follow_up_cleared_at' => 'datetime',
        'follow_up_needed_since' => 'datetime',
        'gender' => Gender::class,
        'classification' => Classification::class,
        'status' => MemberStatus::class,
        'marital_status' => MaritalStatus::class,
        'occupation' => Occupation::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($member) {
            if (empty($member->member_id)) {
                $member->member_id = static::generateMemberId();
            }

            if (empty($member->bible_study_group_id)) {
                $member->bible_study_group_id = BibleStudyGroup::getNextAssignableGroup()?->id;
            }
        });

        // Automatic assignment removed - manual selection only
        // static::created(function ($member) {
        //     $member->assignTraditionalMinistries();
        // });

        // Automatic sync removed - manual selection only
        // static::updating(function ($member) {
        //     if ($member->isDirty('date_of_birth') || $member->isDirty('gender')) {
        //         $member->syncTraditionalMinistries();
        //     }
        // });
    }

    public function bibleStudyGroup(): BelongsTo
    {
        return $this->belongsTo(BibleStudyGroup::class);
    }

    public function ministries(): BelongsToMany
    {
        return $this->belongsToMany(Ministry::class, 'member_ministry')
            ->withPivot('role', 'joined_date')
            ->withTimestamps();
    }

    public function traditional_ministries(): BelongsToMany
    {
        return $this->belongsToMany(Ministry::class, 'member_ministry')
            ->where('type', 'traditional')
            ->withPivot('role', 'joined_date')
            ->withTimestamps();
    }

    public function group_ministries(): BelongsToMany
    {
        return $this->belongsToMany(Ministry::class, 'member_ministry')
            ->where('type', 'group')
            ->withPivot('role', 'joined_date')
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function ministryExecutives(): HasMany
    {
        return $this->hasMany(MinistryExecutive::class);
    }

    public function ledMinistries(): BelongsToMany
    {
        return $this->belongsToMany(Ministry::class, 'ministry_executives')
            ->withPivot('position', 'assigned_date')
            ->withTimestamps();
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'assigned_to_member_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'member_id');
    }

    public function visitorInvites(): HasMany
    {
        return $this->hasMany(Visitor::class, 'invited_by_member_id');
    }

    public function getAgeAttribute(): int
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    public function getAgeGroupAttribute(): string
    {
        $age = $this->age;

        if ($age < 13) {
            return 'children';
        } elseif ($age >= 13 && $age <= 19) {
            return 'teens';
        } elseif ($age >= 20 && $age <= 35) {
            return 'young_adult';
        } else {
            return 'adult';
        }
    }

    public static function generateMemberId(): string
    {
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return "CMS-{$year}-".str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function assignTraditionalMinistries(): void
    {
        if (! $this->date_of_birth || ! $this->gender) {
            return;
        }

        $age = $this->age;
        $gender = $this->gender;

        $currentMinistryIds = $this->ministries()->pluck('ministries.id')->toArray();
        $parentIds = Ministry::whereIn('id', $currentMinistryIds)
            ->pluck('id')
            ->toArray();

        $ministries = Ministry::traditional()
            ->where('is_default', true)
            ->where(function ($query) use ($age) {
                $query->where(function ($q) use ($age) {
                    $q->whereNull('age_min')->orWhere('age_min', '<=', $age);
                })->where(function ($q) use ($age) {
                    $q->whereNull('age_max')->orWhere('age_max', '>=', $age);
                });
            })
            ->where(function ($query) use ($gender) {
                $query->where('gender', 'both')->orWhere('gender', $gender);
            })
            ->get()
            ->filter(function ($ministry) use ($parentIds) {
                foreach ($parentIds as $parentId) {
                    $parent = Ministry::find($parentId);
                    if ($parent && $ministry->isDescendantOf($parent)) {
                        return false;
                    }
                }

                return true;
            });

        $this->ministries()->syncWithoutDetaching($ministries->pluck('id')->toArray());
    }

    public function syncTraditionalMinistries(): void
    {
        $this->traditionalMinistries()->detach();
        $this->assignTraditionalMinistries();
    }
}
