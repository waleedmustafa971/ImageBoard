<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\View\View;

class BoardController extends Controller
{
    public function index(): View
    {
        $boards = Board::all();

        return view('boards.index', compact('boards'));
    }

    public function show(Board $board): View
    {
        $threads = $board->activeThreads()
            ->with(['posts' => function ($query) {
                $query->oldest()->limit(5); // Show OP + first 4 replies
            }])
            ->paginate(10);

        return view('boards.show', compact('board', 'threads'));
    }

    public function catalog(Board $board): View
    {
        $threads = $board->activeThreads()
            ->with(['posts' => function ($query) {
                $query->oldest()->first(); // Only OP
            }])
            ->get();

        return view('boards.catalog', compact('board', 'threads'));
    }
}
