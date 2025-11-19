<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Post;
use App\Models\Thread;
use App\Models\ModerationLog;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupervisorController extends Controller
{
    public function __construct(
        protected ImageService $imageService
    ) {
    }

    // Authentication
    public function showLoginForm(): View
    {
        return view('supervisor.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('supervisor')->attempt($credentials)) {
            $supervisor = Auth::guard('supervisor')->user();

            // Check if supervisor is active
            if (!$supervisor->is_active) {
                Auth::guard('supervisor')->logout();
                return back()->withErrors([
                    'username' => 'Your supervisor account has been deactivated.',
                ])->onlyInput('username');
            }

            $request->session()->regenerate();
            return redirect()->intended(route('supervisor.dashboard'));
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('supervisor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('supervisor.login');
    }

    // Dashboard
    public function dashboard(): View
    {
        $supervisor = Auth::guard('supervisor')->user();
        $boards = $supervisor->boards()->withCount('threads')->get();

        // Get recent moderation logs for this supervisor
        $recentLogs = ModerationLog::where('moderator_type', 'App\Models\Supervisor')
            ->where('moderator_id', $supervisor->id)
            ->with('board')
            ->latest()
            ->limit(20)
            ->get();

        return view('supervisor.dashboard', compact('boards', 'recentLogs'));
    }

    // Thread Management
    public function deleteThread(Board $board, Thread $thread): RedirectResponse
    {
        // Security: Ensure thread belongs to this board
        if ($thread->board_id !== $board->id) {
            abort(404);
        }

        $supervisor = Auth::guard('supervisor')->user();

        // Delete all post images
        foreach ($thread->posts as $post) {
            $this->imageService->delete($post->image_path, $post->image_thumbnail_path);
        }

        // Log the action
        ModerationLog::logAction(
            'App\Models\Supervisor',
            $supervisor->id,
            'delete_thread',
            'thread',
            $thread->id,
            $board->id,
            ['subject' => $thread->subject]
        );

        $thread->delete();

        return back()->with('success', 'Thread deleted successfully!');
    }

    public function togglePinThread(Board $board, Thread $thread): RedirectResponse
    {
        // Security: Ensure thread belongs to this board
        if ($thread->board_id !== $board->id) {
            abort(404);
        }

        $supervisor = Auth::guard('supervisor')->user();
        $newStatus = !$thread->is_pinned;

        $thread->update(['is_pinned' => $newStatus]);

        // Log the action
        ModerationLog::logAction(
            'App\Models\Supervisor',
            $supervisor->id,
            $newStatus ? 'pin_thread' : 'unpin_thread',
            'thread',
            $thread->id,
            $board->id,
            ['subject' => $thread->subject]
        );

        return back()->with('success', 'Thread pin status updated!');
    }

    public function toggleLockThread(Board $board, Thread $thread): RedirectResponse
    {
        // Security: Ensure thread belongs to this board
        if ($thread->board_id !== $board->id) {
            abort(404);
        }

        $supervisor = Auth::guard('supervisor')->user();
        $newStatus = !$thread->is_locked;

        $thread->update(['is_locked' => $newStatus]);

        // Log the action
        ModerationLog::logAction(
            'App\Models\Supervisor',
            $supervisor->id,
            $newStatus ? 'lock_thread' : 'unlock_thread',
            'thread',
            $thread->id,
            $board->id,
            ['subject' => $thread->subject]
        );

        return back()->with('success', 'Thread lock status updated!');
    }

    // Post Management
    public function deletePost(Board $board, Thread $thread, Post $post): RedirectResponse
    {
        // Security: Ensure thread belongs to board and post belongs to thread
        if ($thread->board_id !== $board->id || $post->thread_id !== $thread->id) {
            abort(404);
        }

        $supervisor = Auth::guard('supervisor')->user();

        // Delete post images if exists
        $this->imageService->delete($post->image_path, $post->image_thumbnail_path);

        // Check if this is the OP (first post)
        $isOP = $thread->posts()->oldest()->first()->id === $post->id;

        // Log the action
        ModerationLog::logAction(
            'App\Models\Supervisor',
            $supervisor->id,
            'delete_post',
            'post',
            $post->id,
            $board->id,
            [
                'post_number' => $post->post_number,
                'is_op' => $isOP,
                'thread_subject' => $thread->subject
            ]
        );

        if ($isOP) {
            // If deleting OP, delete entire thread
            foreach ($thread->posts as $threadPost) {
                $this->imageService->delete($threadPost->image_path, $threadPost->image_thumbnail_path);
            }
            $thread->delete();

            return redirect()->route('boards.show', $board)
                ->with('success', 'Thread deleted (OP removed)!');
        }

        $post->delete();

        return back()->with('success', 'Post deleted successfully!');
    }
}
