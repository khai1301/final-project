<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorTeachingArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_profile_id',
        'province_id',
        'ward_id',
    ];

    protected $casts = [
        'tutor_profile_id' => 'integer',
        'province_id' => 'integer',
        'ward_id' => 'integer',
    ];

    /**
     * Get the tutor profile that owns this teaching area.
     */
    public function tutorProfile()
    {
        return $this->belongsTo(TutorProfile::class);
    }

    /**
     * Get the province for this teaching area.
     */
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    /**
     * Get the ward for this teaching area.
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    /**
     * Scope to filter by province.
     */
    public function scopeInProvince($query, int $provinceId)
    {
        return $query->where('province_id', $provinceId);
    }

    /**
     * Scope to filter by ward or province-wide.
     */
    public function scopeInWardOrProvince($query, int $provinceId, ?int $wardId = null)
    {
        return $query->where('province_id', $provinceId)
            ->where(function ($q) use ($wardId) {
                $q->where('ward_id', $wardId)
                  ->orWhereNull('ward_id'); // Province-wide teaching
            });
    }
}
