<?php

namespace App\Http\Middleware;

use App\Models\Ban;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ipAddress = $request->ip();
        $boardId = $request->route('board')?->id ?? null;

        if (Ban::isIpBanned($ipAddress, $boardId)) {
            $ban = Ban::getActiveBan($ipAddress, $boardId);

            $banMessage = 'You have been banned';

            if ($ban->board_id) {
                $banMessage .= ' from /' . $ban->board->slug . '/';
            } else {
                $banMessage .= ' from all boards';
            }

            $banMessage .= '.<br><br><strong>Reason:</strong> ' . e($ban->reason);

            if ($ban->expires_at) {
                $banMessage .= '<br><strong>Expires:</strong> ' . $ban->expires_at->format('Y-m-d H:i:s') . ' (' . $ban->expires_at->diffForHumans() . ')';
            } else {
                $banMessage .= '<br><strong>Duration:</strong> Permanent';
            }

            return back()->with('error', $banMessage);
        }

        return $next($request);
    }
}
