<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'province_id',
        'ward_id',
        'address_detail',
        'avatar',
        'banned_at',
        'is_verified',
        'verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationships
     */
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function tutorProfile()
    {
        return $this->hasOne(TutorProfile::class);
    }

    /**
     * Get the province that the user belongs to.
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Get the ward that the user belongs to.
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Get matchings where user is the student.
     */
    public function studentMatchings()
    {
        return $this->hasMany(Matching::class, 'student_id');
    }

    /**
     * Get matchings where user is the tutor.
     */
    public function tutorMatchings()
    {
        return $this->hasMany(Matching::class, 'tutor_id');
    }

    /**
     * Get matchings where user is the sender.
     */
    public function sentMatchings()
    {
        return $this->hasMany(Matching::class, 'sender_id');
    }

    /**
     * Get all matchings for this user (as student or tutor).
     */
    public function allMatchings()
    {
        return Matching::where('student_id', $this->id)
            ->orWhere('tutor_id', $this->id);
    }

    /**
     * Get accepted connections for this user.
     */
    public function connections()
    {
        return Matching::where(function($q) {
            $q->where('student_id', $this->id)
              ->orWhere('tutor_id', $this->id);
        })->where('status', 'accepted');
    }

    /**
     * Get notifications for this user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadNotificationsCount()
    {
        return $this->notifications()->unread()->count();
    }

    public function verification()
    {
        return $this->hasOne(Verification::class);
    }

    /**
     * Helper Methods
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isTutor(): bool
    {
        return $this->role === 'tutor';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBanned(): bool
    {
        return !is_null($this->banned_at);
    }

    public function isVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function hasBlueTick(): bool
    {
        return $this->verification && $this->verification->blue_tick;
    }

    /**
     * Get the user's avatar URL with fallback to default.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && \Storage::disk('public')->exists($this->avatar)) {
            return \Storage::url($this->avatar);
        }
        
        // Default avatar using UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&size=200&background=3780f6&color=fff';
    }
}
