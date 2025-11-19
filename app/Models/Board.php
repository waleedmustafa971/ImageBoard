<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Board extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_nsfw',
        'post_count',
    ];

    protected $casts = [
        'is_nsfw' => 'boolean',
        'post_count' => 'integer',
    ];

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    public function activeThreads(): HasMany
    {
        return $this->hasMany(Thread::class)
            ->whereNull('deleted_at')
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_bump_at');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function incrementPostCount(): void
    {
        $this->increment('post_count');
    }

    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(Supervisor::class, 'board_supervisor')
            ->withTimestamps();
    }
}
