<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matching extends Model
{
    protected $fillable = [
        'request_id',
        'student_id',
        'tutor_id',
        'sender_id',
        'status',
        'message',
        'contact_unlocked',
        'unlocked_at',
        'unlock_fee',
        'payment_status',
        'payment_method',
        'transaction_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student in this matching.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the tutor in this matching.
     */
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    /**
     * Get the user who initiated the request.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the learning request this matching is for.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Request::class, 'request_id');
    }

    /**
     * Get all notifications for this matching
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'data->matching_id');
    }

    /**
     * Get all payment attempts for this matching
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope for pending matchings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for accepted matchings.
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope for matchings involving a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('student_id', $userId)
              ->orWhere('tutor_id', $userId);
        });
    }

    /**
     * Get the other user in this matching (not the given user).
     */
    public function getOtherUser($userId)
    {
        if ($this->student_id == $userId) {
            return $this->tutor;
        }
        return $this->student;
    }

    /**
     * Get the receiver user (opposite of sender).
     */
    public function getReceiverAttribute()
    {
        if ($this->sender_id == $this->student_id) {
            return $this->tutor;
        }
        return $this->student;
    }

    /**
     * Check if user is the sender.
     */
    public function isSender($userId): bool
    {
        return $this->sender_id == $userId;
    }

    /**
     * Check if user is the receiver.
     */
    public function isReceiver($userId): bool
    {
        return $this->sender_id != $userId;
    }

    /**
     * Accept this matching request.
     */
    public function accept()
    {
        $this->update(['status' => 'accepted']);
        
        // Create notification for sender
        Notification::create([
            'user_id' => $this->sender_id,
            'matching_id' => $this->id,
            'type' => 'connect_accepted',
            'title' => 'Connection Accepted',
            'message' => $this->receiver->name . ' accepted your connection request.',
            'action_url' => route('matching.index'),
        ]);
    }

    /**
     * Decline this matching request.
     */
    public function decline()
    {
        $this->update(['status' => 'declined']);
        
        // Create notification for sender
        Notification::create([
            'user_id' => $this->sender_id,
            'matching_id' => $this->id,
            'type' => 'connect_declined',
            'title' => 'Connection Declined',
            'message' => $this->receiver->name . ' declined your connection request.',
            'action_url' => route('matching.my-requests'),
        ]);
    }

    /**
     * Cancel this matching request.
     */
    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Check if there's an active request between two users.
     * @deprecated Use getConnectionStatus instead
     */
    public static function hasActiveRequest($studentId, $tutorId): bool
    {
        return static::where('student_id', $studentId)
            ->where('tutor_id', $tutorId)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Get connection status for a specific request and tutor
     * Returns: null, 'pending', or 'accepted'
     */
    public static function getConnectionStatus($tutorId, $requestId): ?string
    {
        $matching = static::where('tutor_id', $tutorId)
            ->where('request_id', $requestId)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        return $matching?->status;
    }
}
