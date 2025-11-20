<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Thread;
use App\Models\Post;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Store a new report (public-facing)
     */
    public function store(Request $request, Board $board, Thread $thread, Post $post): RedirectResponse
    {
        // Security: Ensure thread belongs to board and post belongs to thread
        if ($thread->board_id !== $board->id || $post->thread_id !== $thread->id) {
            abort(404);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'in:spam,illegal,harassment,off_topic,other'],
        ]);

        // Prevent duplicate reports from same IP for same post
        $existingReport = Report::where('post_id', $post->id)
            ->where('reporter_ip', $request->ip())
            ->where('created_at', '>', now()->subHours(24))
            ->first();

        if ($existingReport) {
            return back()->with('error', 'You have already reported this post recently.');
        }

        Report::create([
            'post_id' => $post->id,
            'reporter_ip' => $request->ip(),
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Post reported successfully. Moderators will review it.');
    }
}
