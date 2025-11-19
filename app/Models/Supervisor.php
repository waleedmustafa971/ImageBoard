<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supervisor extends Authenticatable
{
    protected $fillable = [
        'username',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Get the boards this supervisor is assigned to
     */
    public function boards(): BelongsToMany
    {
        return $this->belongsToMany(Board::class, 'board_supervisor')
            ->withTimestamps();
    }

    /**
     * Check if supervisor can moderate a specific board
     */
    public function canModerate(Board $board): bool
    {
        return $this->is_active && $this->boards->contains($board->id);
    }

    /**
     * Get moderation logs for this supervisor
     */
    public function moderationLogs()
    {
        return $this->morphMany(ModerationLog::class, 'moderator');
    }
}
