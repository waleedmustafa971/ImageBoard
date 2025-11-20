<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Board;
use App\Models\Thread;
use App\Models\PostImage;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    public function store(StorePostRequest $request, Board $board, Thread $thread): RedirectResponse
    {
        // Security: Ensure thread belongs to this board
        if ($thread->board_id !== $board->id) {
            abort(404);
        }

        // Check if thread is locked
        if ($thread->is_locked) {
            return back()->withErrors(['error' => 'This thread is locked.']);
        }

        // Handle image upload if present (legacy single image)
        $imageData = null;
        if ($request->hasFile('image')) {
            $imageData = $this->imageService->store($request->file('image'));
        }

        // Create reply post
        $post = $thread->posts()->create([
            'post_number' => 0, // Will be set by model boot
            'name' => $request->name,
            'content' => $request->content,
            'image_path' => $imageData['image_path'] ?? null,
            'image_thumbnail_path' => $imageData['thumbnail_path'] ?? null,
        ]);

        // Handle multiple images if present
        if ($request->hasFile('images')) {
            $images = $this->imageService->storeMultiple($request->file('images'));
            foreach ($images as $imageData) {
                PostImage::create([
                    'post_id' => $post->id,
                    'image_path' => $imageData['image_path'],
                    'thumbnail_path' => $imageData['thumbnail_path'],
                    'order' => $imageData['order'],
                ]);
            }
        }

        return redirect()->route('threads.show', [$board, $thread])
            ->with('success', 'Reply posted successfully!');
    }
}
