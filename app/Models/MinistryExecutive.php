<?php

namespace App\Models;

use App\Enums\ExecutivePosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinistryExecutive extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'member_id',
        'position',
        'assigned_date',
    ];

    protected $casts = [
        'position' => ExecutivePosition::class,
        'assigned_date' => 'date',
    ];

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
