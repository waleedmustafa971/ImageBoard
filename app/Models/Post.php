<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    protected $fillable = [
        'thread_id',
        'post_number',
        'name',
        'content',
        'image_path',
        'image_thumbnail_path',
        'ip_address_hash',
    ];

    protected $casts = [
        'post_number' => 'integer',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function hasImage(): bool
    {
        return !is_null($this->image_path);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->image_thumbnail_path ? Storage::url($this->image_thumbnail_path) : null;
    }

    public function getFormattedContentAttribute(): string
    {
        // Convert >>postNumber to clickable links
        return preg_replace_callback(
            '/&gt;&gt;(\d+)/',
            function ($matches) {
                $postNumber = $matches[1];
                return '<a href="#post-' . $postNumber . '" class="quote-link">&gt;&gt;' . $postNumber . '</a>';
            },
            nl2br(e($this->content))
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            // Generate post number based on board's total post count
            $board = $post->thread->board;
            $post->post_number = $board->post_count + 1;

            // Hash IP address
            $post->ip_address_hash = hash('sha256', request()->ip());
        });

        static::created(function ($post) {
            // Increment board post count
            $post->thread->board->incrementPostCount();

            // Increment thread reply count
            $post->thread->incrementReplyCount();

            // Bump thread if within bump limit
            $post->thread->bump();

            // Increment image count if post has image
            if ($post->hasImage()) {
                $post->thread->incrementImageCount();
            }
        });
    }
}
