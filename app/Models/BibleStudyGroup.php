<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BibleStudyGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'leader_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function leader(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function getMemberCountAttribute(): int
    {
        return $this->members()->where('is_active', true)->count();
    }

    public static function getNextAssignableGroup(): ?self
    {
        return self::where('is_active', true)
            ->withCount(['members' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('members_count')
            ->orderBy('name')
            ->first();
    }
}
