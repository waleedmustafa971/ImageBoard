<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoardRequest;
use App\Models\Board;
use App\Models\Post;
use App\Models\Thread;
use App\Models\Supervisor;
use App\Models\ModerationLog;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $supervisorCount = Supervisor::count();
        $totalThreads = Thread::count();
        $totalPosts = Post::count();

        // Get recent moderation logs across all moderators
        $recentLogs = ModerationLog::with(['board'])
            ->latest()
            ->limit(15)
            ->get();

        return view('admin.dashboard', compact('boards', 'supervisorCount', 'totalThreads', 'totalPosts', 'recentLogs'));
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

        $admin = Auth::guard('admin')->user();

        // Delete all post images
        foreach ($thread->posts as $post) {
            $this->imageService->delete($post->image_path, $post->image_thumbnail_path);
        }

        // Log the action
        ModerationLog::logAction(
            'App\Models\Admin',
            $admin->id,
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

        $admin = Auth::guard('admin')->user();
        $newStatus = !$thread->is_pinned;

        $thread->update(['is_pinned' => $newStatus]);

        // Log the action
        ModerationLog::logAction(
            'App\Models\Admin',
            $admin->id,
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

        $admin = Auth::guard('admin')->user();
        $newStatus = !$thread->is_locked;

        $thread->update(['is_locked' => $newStatus]);

        // Log the action
        ModerationLog::logAction(
            'App\Models\Admin',
            $admin->id,
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

        $admin = Auth::guard('admin')->user();

        // Delete post images if exists
        $this->imageService->delete($post->image_path, $post->image_thumbnail_path);

        // Check if this is the OP (first post)
        $isOP = $thread->posts()->oldest()->first()->id === $post->id;

        // Log the action
        ModerationLog::logAction(
            'App\Models\Admin',
            $admin->id,
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

    // Supervisor Management
    public function supervisorIndex(): View
    {
        $supervisors = Supervisor::withCount('boards')->get();
        return view('admin.supervisors.index', compact('supervisors'));
    }

    public function supervisorCreate(): View
    {
        $boards = Board::all();
        return view('admin.supervisors.create', compact('boards'));
    }

    public function supervisorStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:supervisors'],
            'email' => ['required', 'email', 'max:255', 'unique:supervisors'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['boolean'],
            'boards' => ['array'],
            'boards.*' => ['exists:boards,id'],
        ]);

        $supervisor = Supervisor::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (!empty($validated['boards'])) {
            $supervisor->boards()->attach($validated['boards']);
        }

        return redirect()->route('admin.supervisors.index')
            ->with('success', 'Supervisor created successfully!');
    }

    public function supervisorEdit(Supervisor $supervisor): View
    {
        $boards = Board::all();
        $assignedBoardIds = $supervisor->boards->pluck('id')->toArray();
        return view('admin.supervisors.edit', compact('supervisor', 'boards', 'assignedBoardIds'));
    }

    public function supervisorUpdate(Request $request, Supervisor $supervisor): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:supervisors,username,' . $supervisor->id],
            'email' => ['required', 'email', 'max:255', 'unique:supervisors,email,' . $supervisor->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['boolean'],
            'boards' => ['array'],
            'boards.*' => ['exists:boards,id'],
        ]);

        $updateData = [
            'username' => $validated['username'],
            'email' => $validated['email'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $supervisor->update($updateData);

        // Sync board assignments
        $supervisor->boards()->sync($validated['boards'] ?? []);

        return redirect()->route('admin.supervisors.index')
            ->with('success', 'Supervisor updated successfully!');
    }

    public function supervisorDestroy(Supervisor $supervisor): RedirectResponse
    {
        $supervisor->delete();

        return redirect()->route('admin.supervisors.index')
            ->with('success', 'Supervisor deleted successfully!');
    }

    // Activity Monitoring
    public function activityLogs(Request $request): View
    {
        $query = ModerationLog::with(['board']);

        // Filter by board
        if ($request->has('board_id') && $request->board_id) {
            $query->where('board_id', $request->board_id);
        }

        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Filter by moderator type
        if ($request->has('moderator_type') && $request->moderator_type) {
            $query->where('moderator_type', $request->moderator_type);
        }

        $logs = $query->latest()->paginate(50);
        $boards = Board::all();

        // Get unique actions for filter
        $actions = ModerationLog::distinct('action')->pluck('action');

        return view('admin.activity-logs', compact('logs', 'boards', 'actions'));
    }
}
