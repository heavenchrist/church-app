<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\MinistryType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ministry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'parent_id',
        'age_min',
        'age_max',
        'gender',
        'type',
        'is_default',
        'is_active',
        'is_assignable',
        'sort',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'is_assignable' => 'boolean',
        'gender' => Gender::class,
        'type' => MinistryType::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($model) {
            if (is_null($model->parent_id) && in_array($model->type, ['group', MinistryType::Traditional])) {
                $model->is_assignable = true;
            }
        });
        static::creating(function ($ministry) {
            if (empty($ministry->slug)) {
                $ministry->slug = Str::slug($ministry->name);
            }
        });

        static::updating(function ($ministry) {
            if ($ministry->isDirty('name')) {
                $ministry->slug = Str::slug($ministry->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Ministry::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Ministry::class, 'parent_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_ministry')
            ->withPivot('role', 'joined_date')
            ->withTimestamps();
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function executives(): HasMany
    {
        return $this->hasMany(MinistryExecutive::class);
    }

    public function executiveMembers(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'ministry_executives')
            ->withPivot('position', 'assigned_date')
            ->withTimestamps();
    }

    public function getMemberCountAttribute(): int
    {
        return $this->members()->where('is_active', true)->count();
    }

    public function isForAge(int $age): bool
    {
        if ($this->age_min !== null && $age < $this->age_min) {
            return false;
        }
        if ($this->age_max !== null && $age > $this->age_max) {
            return false;
        }

        return true;
    }

    public function getAgeRangeAttribute()
    {
        return "{$this->age_min} - {$this->age_max}";
    }

    public function isForGender(?string $gender): bool
    {
        if ($this->gender === 'both') {
            return true;
        }

        return $this->gender === $gender;
    }

    public function scopeTraditional($query)
    {
        return $query->where('type', 'traditional');
    }

    public function scopeGroups($query)
    {
        return $query->where('type', 'group');
    }

    public static function getAllowedGenders(?Gender $parentGender): array
    {
        return match ($parentGender) {
            Gender::Male => [Gender::Male],
            Gender::Female => [Gender::Female],
            default => [Gender::Male, Gender::Female, Gender::Both],
        };
    }

    public function isDescendantOf(Ministry $parent): bool
    {
        $current = $this->parent;
        while ($current) {
            if ($current->id === $parent->id) {
                return true;
            }
            $current = $current->parent;
        }

        return false;
    }

    public function getDescendantIds(): array
    {
        $descendants = [];
        foreach ($this->children as $child) {
            $descendants[] = $child->id;
            $descendants = array_merge($descendants, $child->getDescendantIds());
        }

        return $descendants;
    }

    public function getAncestorIds(): array
    {
        $ancestors = [];
        $current = $this->parent;
        while ($current) {
            $ancestors[] = $current->id;
            $current = $current->parent;
        }

        return $ancestors;
    }
}
