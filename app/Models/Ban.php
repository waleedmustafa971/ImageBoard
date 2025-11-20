<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Ban extends Model
{
    protected $fillable = [
        'ip_address',
        'board_id',
        'reason',
        'banned_by_type',
        'banned_by_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function bannedBy(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if a ban is active (not expired)
     */
    public function isActive(): bool
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }

    /**
     * Check if an IP is banned for a specific board or globally
     */
    public static function isIpBanned(string $ipAddress, ?int $boardId = null): bool
    {
        return self::where('ip_address', $ipAddress)
            ->where(function ($query) use ($boardId) {
                $query->whereNull('board_id') // Global ban
                    ->orWhere('board_id', $boardId); // Board-specific ban
            })
            ->where(function ($query) {
                $query->whereNull('expires_at') // Permanent ban
                    ->orWhere('expires_at', '>', now()); // Not expired
            })
            ->exists();
    }

    /**
     * Get the active ban for an IP address
     */
    public static function getActiveBan(string $ipAddress, ?int $boardId = null): ?self
    {
        return self::where('ip_address', $ipAddress)
            ->where(function ($query) use ($boardId) {
                $query->whereNull('board_id')
                    ->orWhere('board_id', $boardId);
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->with(['board', 'bannedBy'])
            ->first();
    }
}
