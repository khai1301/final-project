<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TutorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'education',
        'experience_years',
        'hourly_rate_min',
        'hourly_rate_max',
        'teaching_areas',
        'bio',
        'cv_path',
        'is_approved',
    ];

    protected $casts = [
        'teaching_areas' => 'array',
        'is_approved' => 'boolean',
        'hourly_rate_min' => 'decimal:2',
        'hourly_rate_max' => 'decimal:2',
    ];

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to Certificates
     */
    public function certificates()
    {
        return $this->hasMany(TutorCertificate::class, 'tutor_profile_id');
    }

    /**
     * Relationship to Subjects (Many-to-Many)
     */
    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'tutor_profile_subject',  // pivot table name
            'tutor_profile_id',        // foreign key on pivot table for this model
            'subject_id'               // foreign key on pivot table for related model
        );
    }

    /**
     * Teaching areas relationship
     */
    public function teachingAreas()
    {
        return $this->hasMany(TutorTeachingArea::class, 'tutor_profile_id');
    }

    /**
     * Get available time slots for this tutor.
     */
    public function availableTimeSlots()
    {
        return $this->belongsToMany(
            TimeSlot::class,
            'tutor_availabilities',
            'tutor_profile_id',
            'time_slot_id'
        )->withTimestamps();
    }
}
