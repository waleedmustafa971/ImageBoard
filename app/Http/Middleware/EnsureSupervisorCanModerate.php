<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Board;

class EnsureSupervisorCanModerate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supervisor = auth('supervisor')->user();

        // Get board from route parameter
        $board = $request->route('board');

        // If board is a slug/string, resolve it to a Board model
        if (is_string($board)) {
            $board = Board::where('slug', $board)->firstOrFail();
        }

        // Check if supervisor can moderate this board
        if (!$supervisor || !$supervisor->canModerate($board)) {
            abort(403, 'You do not have permission to moderate this board.');
        }

        return $next($request);
    }
}
