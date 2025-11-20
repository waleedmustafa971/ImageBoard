@extends('layouts.app')

@section('title', 'Manage Bans - Admin - ImageBoard')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.dashboard') }}">&larr; Back to Dashboard</a>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="color: #AF0A0F;">Manage Bans</h2>
    <a href="{{ route('admin.bans.create') }}" style="padding: 10px 20px; background: #D6DAF0; border: 1px solid #B7C5D9; text-decoration: none; color: #000;">Create New Ban</a>
</div>

<div style="background: #F0E0D6; padding: 15px; border: 1px solid #D9BFB7; margin-bottom: 20px;">
    <h3 style="margin-top: 0;">Filters</h3>
    <form action="{{ route('admin.bans.index') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: end;">
        <div>
            <label for="board_id" style="display: block; margin-bottom: 5px;">Board</label>
            <select id="board_id" name="board_id" style="padding: 5px;">
                <option value="">All Boards (Global)</option>
                @foreach($boards as $board)
                    <option value="{{ $board->id }}" {{ request('board_id') == $board->id ? 'selected' : '' }}>
                        /{{ $board->slug }}/ - {{ $board->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="status" style="display: block; margin-bottom: 5px;">Status</label>
            <select id="status" name="status" style="padding: 5px;">
                <option value="">All Bans</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
        </div>

        <div>
            <button type="submit" style="padding: 5px 15px; background: #D6DAF0; border: 1px solid #B7C5D9; cursor: pointer;">Apply Filters</button>
            <a href="{{ route('admin.bans.index') }}" style="display: inline-block; padding: 5px 15px; background: #EEE; border: 1px solid #CCC; text-decoration: none; color: #000;">Clear</a>
        </div>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>IP Address</th>
            <th>Board</th>
            <th>Reason</th>
            <th>Banned By</th>
            <th>Expires</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bans as $ban)
        <tr>
            <td><code>{{ $ban->ip_address }}</code></td>
            <td>
                @if($ban->board_id)
                    /{{ $ban->board->slug }}/
                @else
                    <strong style="color: #AF0A0F;">GLOBAL</strong>
                @endif
            </td>
            <td>{{ Str::limit($ban->reason, 50) }}</td>
            <td>
                @if($ban->banned_by_type == 'App\Models\Admin')
                    <span style="color: #AF0A0F;">Admin</span>
                @else
                    <span style="color: #0F7A0F;">Supervisor</span>
                @endif
            </td>
            <td>
                @if($ban->expires_at)
                    {{ $ban->expires_at->format('Y-m-d H:i') }}<br>
                    <small>({{ $ban->expires_at->diffForHumans() }})</small>
                @else
                    <strong>Permanent</strong>
                @endif
            </td>
            <td>
                @if($ban->isActive())
                    <span style="color: green; font-weight: bold;">Active</span>
                @else
                    <span style="color: #999;">Expired</span>
                @endif
            </td>
            <td>{{ $ban->created_at->format('Y-m-d H:i') }}</td>
            <td>
                <form action="{{ route('admin.bans.destroy', $ban) }}" method="POST" style="display: inline;" onsubmit="return confirm('Remove this ban?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; color: #D00; cursor: pointer; padding: 0; font-size: inherit;">Remove</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align: center; padding: 20px; color: #666;">No bans found. <a href="{{ route('admin.bans.create') }}">Create one now</a>.</td>
        </tr>
        @endforelse
    </tbody>
</table>

@if($bans->hasPages())
<div style="margin-top: 20px; text-align: center;">
    {{ $bans->links() }}
</div>
@endif
@endsection
