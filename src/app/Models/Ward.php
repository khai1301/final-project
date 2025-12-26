<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'code',
        'name',
        'type',
        'province_code',
    ];

    /**
     * Get the province this ward belongs to.
     */
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    /**
     * Get all users in this ward.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'ward_id');
    }

    /**
     * Get all tutor teaching areas in this ward.
     */
    public function tutorTeachingAreas()
    {
        return $this->hasMany(TutorTeachingArea::class, 'ward_id');
    }

    /**
     * Scope to filter by province.
     */
    public function scopeInProvince($query, string $provinceCode)
    {
        return $query->where('province_code', $provinceCode);
    }

    /**
     * Scope to search by name.
     */
    public function scopeSearchByName($query, string $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }
}
