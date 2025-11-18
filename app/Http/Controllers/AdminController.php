<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoardRequest;
use App\Models\Board;
use App\Models\Post;
use App\Models\Thread;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(
        protected ImageService $imageService
    ) {
    }

    // Authentication
    public function showLoginForm(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    // Dashboard
    public function dashboard(): View
    {
        $boards = Board::withCount('threads')->get();

        return view('admin.dashboard', compact('boards'));
    }

    // Board Management
    public function boardIndex(): View
    {
        $boards = Board::all();

        return view('admin.boards.index', compact('boards'));
    }

    public function boardCreate(): View
    {
        return view('admin.boards.create');
    }

    public function boardStore(StoreBoardRequest $request): RedirectResponse
    {
        Board::create($request->validated());

        return redirect()->route('admin.boards.index')
            ->with('success', 'Board created successfully!');
    }

    public function boardEdit(Board $board): View
    {
        return view('admin.boards.edit', compact('board'));
    }

    public function boardUpdate(StoreBoardRequest $request, Board $board): RedirectResponse
    {
        $board->update($request->validated());

        return redirect()->route('admin.boards.index')
            ->with('success', 'Board updated successfully!');
    }

    public function boardDestroy(Board $board): RedirectResponse
    {
        $board->delete();

        return redirect()->route('admin.boards.index')
            ->with('success', 'Board deleted successfully!');
    }

    // Thread Management
    public function deleteThread(Board $board, Thread $thread): RedirectResponse
    {
        // Security: Ensure thread belongs to this board
        if ($thread->board_id !== $board->id) {
            abort(404);
        }

        // Delete all post images
        foreach ($thread->posts as $post) {
            $this->imageService->delete($post->image_path, $post->image_thumbnail_path);
        }

        $thread->delete();

        return back()->with('success', 'Thread deleted successfully!');
    }

    public function togglePinThread(Board $board, Thread $thread): RedirectResponse
    {
        // Security: Ensure thread belongs to this board
        if ($thread->board_id !== $board->id) {
            abort(404);
        }

        $thread->update(['is_pinned' => !$thread->is_pinned]);

        return back()->with('success', 'Thread pin status updated!');
    }

    public function toggleLockThread(Board $board, Thread $thread): RedirectResponse
    {
        // Security: Ensure thread belongs to this board
        if ($thread->board_id !== $board->id) {
            abort(404);
        }

        $thread->update(['is_locked' => !$thread->is_locked]);

        return back()->with('success', 'Thread lock status updated!');
    }

    // Post Management
    public function deletePost(Board $board, Thread $thread, Post $post): RedirectResponse
    {
        // Security: Ensure thread belongs to board and post belongs to thread
        if ($thread->board_id !== $board->id || $post->thread_id !== $thread->id) {
            abort(404);
        }

        // Delete post images if exists
        $this->imageService->delete($post->image_path, $post->image_thumbnail_path);

        // Check if this is the OP (first post)
        $isOP = $thread->posts()->oldest()->first()->id === $post->id;

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
