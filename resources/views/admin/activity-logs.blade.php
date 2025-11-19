@extends('layouts.app')

@section('title', 'Activity Logs - Admin - ImageBoard')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.dashboard') }}">&larr; Back to Dashboard</a>
</div>

<h2 style="color: #AF0A0F; margin-bottom: 20px;">Activity Logs</h2>

<div style="background: #F0E0D6; padding: 15px; border: 1px solid #D9BFB7; margin-bottom: 20px;">
    <h3 style="margin-top: 0;">Filters</h3>
    <form action="{{ route('admin.activity.logs') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: end;">
        <div>
            <label for="board_id" style="display: block; margin-bottom: 5px;">Board</label>
            <select id="board_id" name="board_id" style="padding: 5px;">
                <option value="">All Boards</option>
                @foreach($boards as $board)
                    <option value="{{ $board->id }}" {{ request('board_id') == $board->id ? 'selected' : '' }}>
                        /{{ $board->slug }}/ - {{ $board->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="action" style="display: block; margin-bottom: 5px;">Action</label>
            <select id="action" name="action" style="padding: 5px;">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $action)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="moderator_type" style="display: block; margin-bottom: 5px;">Moderator Type</label>
            <select id="moderator_type" name="moderator_type" style="padding: 5px;">
                <option value="">All Types</option>
                <option value="App\Models\Admin" {{ request('moderator_type') == 'App\Models\Admin' ? 'selected' : '' }}>Admin</option>
                <option value="App\Models\Supervisor" {{ request('moderator_type') == 'App\Models\Supervisor' ? 'selected' : '' }}>Supervisor</option>
            </select>
        </div>

        <div>
            <button type="submit" style="padding: 5px 15px; background: #D6DAF0; border: 1px solid #B7C5D9; cursor: pointer;">Apply Filters</button>
            <a href="{{ route('admin.activity.logs') }}" style="display: inline-block; padding: 5px 15px; background: #EEE; border: 1px solid #CCC; text-decoration: none; color: #000;">Clear</a>
        </div>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Date & Time</th>
            <th>Moderator</th>
            <th>Type</th>
            <th>Action</th>
            <th>Board</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        @forelse($logs as $log)
        <tr>
            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
            <td>
                @if($log->moderator_type == 'App\Models\Admin')
                    <span style="color: #AF0A0F; font-weight: bold;">Admin</span> #{{ $log->moderator_id }}
                @else
                    <span style="color: #0F7A0F; font-weight: bold;">Supervisor</span> #{{ $log->moderator_id }}
                @endif
            </td>
            <td>{{ ucfirst(str_replace('App\\Models\\', '', $log->moderator_type)) }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $log->action)) }}</td>
            <td>/{{ $log->board->slug }}/</td>
            <td>
                @if($log->target_type == 'thread')
                    Thread #{{ $log->target_id }}
                    @if(isset($log->metadata['subject']))
                        - {{ Str::limit($log->metadata['subject'], 30) }}
                    @endif
                @elseif($log->target_type == 'post')
                    Post #{{ $log->metadata['post_number'] ?? $log->target_id }}
                    @if(isset($log->metadata['is_op']) && $log->metadata['is_op'])
                        (OP)
                    @endif
                    @if(isset($log->metadata['thread_subject']))
                        in: {{ Str::limit($log->metadata['thread_subject'], 20) }}
                    @endif
                @else
                    {{ $log->target_type }} #{{ $log->target_id }}
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align: center; padding: 20px; color: #666;">No activity logs found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

@if($logs->hasPages())
<div style="margin-top: 20px; text-align: center;">
    {{ $logs->links() }}
</div>
@endif
@endsection
