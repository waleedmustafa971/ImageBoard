<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'board_id',
        'subject',
        'is_pinned',
        'is_locked',
        'reply_count',
        'image_count',
        'last_bump_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'reply_count' => 'integer',
        'image_count' => 'integer',
        'last_bump_at' => 'datetime',
    ];

    const BUMP_LIMIT = 500;

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)->orderBy('created_at');
    }

    public function originalPost(): ?Post
    {
        return $this->posts()->oldest()->first();
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Post::class)->skip(1); // Skip OP
    }

    public function canBump(): bool
    {
        return $this->reply_count < self::BUMP_LIMIT && !$this->is_locked;
    }

    public function bump(): void
    {
        if ($this->canBump()) {
            $this->update(['last_bump_at' => now()]);
        }
    }

    public function incrementReplyCount(): void
    {
        $this->increment('reply_count');
    }

    public function incrementImageCount(): void
    {
        $this->increment('image_count');
    }
}
