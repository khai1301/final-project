<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LearningMode extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
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

        static::creating(function ($mode) {
            if (empty($mode->slug)) {
                $mode->slug = Str::slug($mode->name);
            }
        });

        static::updating(function ($mode) {
            if ($mode->isDirty('name')) {
                $mode->slug = Str::slug($mode->name);
            }
        });
    }

    /**
     * Get the requests for this learning mode.
     */
    public function requests()
    {
        return $this->hasMany(Request::class, 'learning_mode_id');
    }

    /**
     * Scope a query to only include active learning modes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
