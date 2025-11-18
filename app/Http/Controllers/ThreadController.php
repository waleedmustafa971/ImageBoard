<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThreadRequest;
use App\Models\Board;
use App\Models\Post;
use App\Models\Thread;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ThreadController extends Controller
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    public function create(Board $board): View
    {
        return view('threads.create', compact('board'));
    }

    public function store(StoreThreadRequest $request, Board $board): RedirectResponse
    {
        // Create thread
        $thread = $board->threads()->create([
            'subject' => $request->subject,
            'last_bump_at' => now(),
        ]);

        // Handle image upload
        $imageData = $this->imageService->store($request->file('image'));

        // Create original post (OP)
        $thread->posts()->create([
            'post_number' => 0, // Will be set by model boot
            'name' => $request->name,
            'content' => $request->content,
            'image_path' => $imageData['image_path'],
            'image_thumbnail_path' => $imageData['thumbnail_path'],
        ]);

        return redirect()->route('threads.show', [$board, $thread])
            ->with('success', 'Thread created successfully!');
    }

    public function show(Board $board, Thread $thread): View
    {
        // Security: Ensure thread belongs to this board
        if ($thread->board_id !== $board->id) {
            abort(404);
        }

        $thread->load(['posts' => function ($query) {
            $query->orderBy('created_at');
        }]);

        return view('threads.show', compact('board', 'thread'));
    }
}
