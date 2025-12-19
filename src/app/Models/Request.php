<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'title', 'subject', 'grade', 'education_level', 'skills',
        'mode', 'schedule', 'budget_min', 'budget_max', 'description',
        'status', 'location_type', 'address',
        'subject_id', 'education_level_id', 'learning_mode_id',
    ];

    protected $casts = [
        'skills' => 'array',
        'schedule' => 'array',
    ];

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
    public function subjectRelation()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the education level for the request.
     */
    public function educationLevelRelation()
    {
        return $this->belongsTo(EducationLevel::class, 'education_level_id');
    }

    /**
     * Get the learning mode for the request.
     */
    public function learningModeRelation()
    {
        return $this->belongsTo(LearningMode::class, 'learning_mode_id');
    }
}
