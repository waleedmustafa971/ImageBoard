<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModerationLog extends Model
{
    protected $fillable = [
        'moderator_type',
        'moderator_id',
        'action',
        'target_type',
        'target_id',
        'board_id',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Get the board associated with this log
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Get the moderator (admin or supervisor)
     */
    public function moderator()
    {
        return $this->morphTo();
    }

    /**
     * Create a log entry
     */
    public static function logAction(
        string $moderatorType,
        int $moderatorId,
        string $action,
        string $targetType,
        int $targetId,
        ?int $boardId = null,
        ?array $details = null
    ): self {
        return self::create([
            'moderator_type' => $moderatorType,
            'moderator_id' => $moderatorId,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'board_id' => $boardId,
            'details' => $details,
        ]);
    }
}
