<?php

namespace App\Models;

use App\Enums\AttendanceType;
use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'topic',
        'description',
        'service_date',
        'service_type',
        'is_active',
    ];

    protected $casts = [
        'service_date' => 'date',
        'is_active' => 'boolean',
        'service_type' => ServiceType::class,
    ];

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceOfficers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'service_attendance_officers');
    }

    public function getPresentCountAttribute(): int
    {
        return $this->attendances()->where('attendance_type', AttendanceType::Present)->count();
    }

    public function getAbsentCountAttribute(): int
    {
        return $this->attendances()->where('attendance_type', AttendanceType::Absent)->count();
    }
}
