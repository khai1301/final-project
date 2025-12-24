<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subject) {
            if (empty($subject->slug)) {
                $subject->slug = Str::slug($subject->name);
            }
        });

        static::updating(function ($subject) {
            if ($subject->isDirty('name')) {
                $subject->slug = Str::slug($subject->name);
            }
        });
    }

    /**
     * Get the requests for this subject.
     */
    public function requests()
    {
        return $this->hasMany(Request::class, 'subject_id');
    }

    /**
     * Get the tutor profiles that teach this subject.
     */
    public function tutorProfiles()
    {
        return $this->belongsToMany(
            TutorProfile::class,
            'tutor_profile_subject',
            'subject_id',           // foreign key on pivot for this model
            'tutor_profile_id'      // foreign key on pivot for related model
        );
    }

    /**
     * Scope a query to only include active subjects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
