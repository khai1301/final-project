<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EducationLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($level) {
            if (empty($level->slug)) {
                $level->slug = Str::slug($level->name);
            }
        });

        static::updating(function ($level) {
            if ($level->isDirty('name')) {
                $level->slug = Str::slug($level->name);
            }
        });
    }

    /**
     * Get the requests for this education level.
     */
    public function requests()
    {
        return $this->hasMany(Request::class, 'education_level_id');
    }

    /**
     * Scope a query to only include active education levels.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by the order column.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
