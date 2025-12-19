<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorCertificate extends Model
{
    protected $fillable = [
        'tutor_profile_id',
        'name',
        'file_path',
        'file_type',
        'file_size',
    ];

    /**
     * Get the tutor profile that owns this certificate.
     */
    public function tutorProfile(): BelongsTo
    {
        return $this->belongsTo(TutorProfile::class);
    }

    /**
     * Get the full URL to the certificate file.
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
