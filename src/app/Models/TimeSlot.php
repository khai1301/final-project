<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
        'duration_minutes',
        'label',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_minutes' => 'integer',
        'day_of_week' => 'integer',
    ];

    // Day constants
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    /**
     * Get student requests using this time slot.
     */
    public function studentRequests()
    {
        return $this->belongsToMany(
            Request::class,
            'student_request_schedules',
            'time_slot_id',
            'request_id'
        )->withTimestamps();
    }

    /**
     * Get tutor profiles available at this time slot.
     */
    public function tutorProfiles()
    {
        return $this->belongsToMany(
            TutorProfile::class,
            'tutor_availabilities',
            'time_slot_id',
            'tutor_profile_id'
        )->withTimestamps();
    }

    /**
     * Scope to get only active time slots.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get time slots for a specific day.
     */
    public function scopeForDay($query, int $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Get formatted label for display.
     */
    public function getFormattedLabelAttribute(): string
    {
        if ($this->label) {
            return $this->label;
        }

        return $this->getDayName() . ' ' . 
               date('H:i', strtotime($this->start_time)) . '-' . 
               date('H:i', strtotime($this->end_time));
    }

    /**
     * Get Vietnamese day name from integer.
     */
    public function getDayName(): string
    {
        $days = [
            1 => 'Thứ 2',
            2 => 'Thứ 3',
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
            7 => 'Chủ nhật',
        ];

        return $days[$this->day_of_week] ?? 'Unknown';
    }

    /**
     * Get English day name from integer.
     */
    public function getDayNameEn(): string
    {
        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];

        return $days[$this->day_of_week] ?? 'Unknown';
    }
}
