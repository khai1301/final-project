<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subjects',
        'education',
        'experience_years',
        'hourly_rate_min',
        'hourly_rate_max',
        'teaching_areas',
        'bio',
        'certificates',
        'is_approved',
        'rating_avg',
        'review_count',
        'availability',
    ];

    protected $casts = [
        'subjects' => 'array',
        'teaching_areas' => 'array',
        'certificates' => 'array',
        'availability' => 'array',
        'is_approved' => 'boolean',
        'hourly_rate_min' => 'decimal:2',
        'hourly_rate_max' => 'decimal:2',
        'rating_avg' => 'decimal:2',
    ];

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
