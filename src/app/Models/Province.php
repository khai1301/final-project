<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'code',
        'name',
        'type',
    ];

    /**
     * Get all wards belonging to this province.
     */
    public function wards()
    {
        return $this->hasMany(Ward::class, 'province_code', 'code');
    }

    /**
     * Get all users in this province.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'province_id');
    }

    /**
     * Get all tutor teaching areas in this province.
     */
    public function tutorTeachingAreas()
    {
        return $this->hasMany(TutorTeachingArea::class, 'province_id');
    }

    /**
     * Scope to search by name.
     */
    public function scopeSearchByName($query, string $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }
}
