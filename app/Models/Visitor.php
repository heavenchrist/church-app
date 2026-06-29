<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\VisitorStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'gender',
        'address',
        'visit_date',
        'invited_by_member',
        'invited_by_member_id',
        'invited_by_name',
        'how_heard_about_church',
        'remarks',
        'is_followed_up',
        'followed_up_at',
        'status',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'is_followed_up' => 'boolean',
        'followed_up_at' => 'datetime',
        'status' => VisitorStatus::class,
        'invited_by_member' => 'boolean',
        'gender' => Gender::class,
    ];

    protected static function booted()
    {
        static::saving(function ($visitor) {
            if ($visitor->invited_by_member) {
                $visitor->invited_by_name = null;
            } else {
                $visitor->invited_by_member_id = null;
            }
        });
    }

    public function invitedByMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'invited_by_member_id');
    }

    public function getInvitedByAttribute(): string
    {
        return $this->invited_by_member === true
            ? $this->invitedByMember?->full_name ?? 'N/A'
            : $this->invited_by_name ?? 'N/A';
    }
}
