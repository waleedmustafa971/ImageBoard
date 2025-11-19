@extends('layouts.app')

@section('title', 'Supervisor Dashboard - ImageBoard')

@section('content')
<h2 style="color: #0F7A0F; margin-bottom: 20px;">Supervisor Dashboard</h2>

<div style="margin-bottom: 30px;">
    <h3>Welcome, {{ auth('supervisor')->user()->username }}!</h3>
    <p>You have moderation access to {{ $boards->count() }} board(s).</p>
</div>

<div style="margin-bottom: 30px;">
    <h3>Your Boards</h3>
    <table>
        <thead>
            <tr>
                <th>Board</th>
                <th>Slug</th>
                <th>Total Threads</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($boards as $board)
            <tr>
                <td>{{ $board->name }}</td>
                <td>/{{ $board->slug }}/</td>
                <td>{{ $board->threads_count }}</td>
                <td>
                    <a href="{{ route('boards.show', $board) }}" style="color: #34345C;">View Board</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px; color: #666;">You have not been assigned to any boards yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-bottom: 30px;">
    <h3>Recent Moderation Activity</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Action</th>
                <th>Board</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentLogs as $log)
            <tr>
                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $log->action)) }}</td>
                <td>/{{ $log->board->slug }}/</td>
                <td>
                    @if(isset($log->metadata['subject']))
                        Thread: {{ $log->metadata['subject'] }}
                    @elseif(isset($log->metadata['post_number']))
                        Post #{{ $log->metadata['post_number'] }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px; color: #666;">No recent activity.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 30px;">
    <form action="{{ route('supervisor.logout') }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" style="background: #D6DAF0; border: 1px solid #B7C5D9; padding: 10px 20px; cursor: pointer;">Logout</button>
    </form>
</div>
@endsection
