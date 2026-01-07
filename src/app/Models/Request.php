<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'title', 'skills',
        'budget_min', 'budget_max', 'description',
        'status', 'address',
        'subject_id', 'education_level_id', 'learning_mode_id',
        'province_id', 'ward_id', 'address_detail',
        'is_matched',
    ];

    protected $casts = [
        'skills' => 'array',
        'is_matched' => 'boolean',
    ];

    /**
     * Scope to get only available (unmatched) requests
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'open')
                     ->where('is_matched', false);
    }

    /**
     * Get the student that owns the request.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the subject for the request.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the education level for the request.
     */
    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class, 'education_level_id');
    }

    /**
     * Get the learning mode for the request.
     */
    public function learningMode()
    {
        return $this->belongsTo(LearningMode::class, 'learning_mode_id');
    }

    /**
     * Get the province for the request.
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Get the ward for the request.
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Get the time slots for this request.
     */
    public function timeSlots()
    {
        return $this->belongsToMany(
            TimeSlot::class,
            'student_request_schedules',
            'request_id',
            'time_slot_id'
        )->withTimestamps();
    }

    /**
     * Get all matchings (connections) for this request.
     */
    public function matchings()
    {
        return $this->hasMany(\App\Models\Matching::class, 'request_id');
    }
}
