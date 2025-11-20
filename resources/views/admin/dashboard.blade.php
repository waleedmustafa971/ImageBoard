@extends('layouts.app')

@section('title', 'Admin Dashboard - ImageBoard')

@section('content')
<h2 style="color: #AF0A0F; margin-bottom: 20px;">Admin Dashboard</h2>

<div style="margin-bottom: 30px;">
    <h3>Quick Actions</h3>
    <div style="margin: 15px 0;">
        <a href="{{ route('admin.boards.index') }}" style="display: inline-block; padding: 10px 20px; background: #D6DAF0; border: 1px solid #B7C5D9; text-decoration: none; color: #000; margin-right: 10px;">Manage Boards</a>
        <a href="{{ route('admin.boards.create') }}" style="display: inline-block; padding: 10px 20px; background: #D6DAF0; border: 1px solid #B7C5D9; text-decoration: none; color: #000; margin-right: 10px;">Create New Board</a>
        <a href="{{ route('admin.supervisors.index') }}" style="display: inline-block; padding: 10px 20px; background: #D6DAF0; border: 1px solid #B7C5D9; text-decoration: none; color: #000; margin-right: 10px;">Manage Supervisors</a>
        <a href="{{ route('admin.bans.index') }}" style="display: inline-block; padding: 10px 20px; background: #D6DAF0; border: 1px solid #B7C5D9; text-decoration: none; color: #000; margin-right: 10px;">Manage Bans</a>
        <a href="{{ route('admin.reports.index') }}" style="display: inline-block; padding: 10px 20px; background: #D6DAF0; border: 1px solid #B7C5D9; text-decoration: none; color: #000; margin-right: 10px;">Review Reports</a>
        <a href="{{ route('admin.activity.logs') }}" style="display: inline-block; padding: 10px 20px; background: #D6DAF0; border: 1px solid #B7C5D9; text-decoration: none; color: #000;">Activity Logs</a>
    </div>
</div>

<div>
    <h3>Board Statistics</h3>
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
                    <a href="{{ route('boards.show', $board) }}" style="color: #34345C; margin-right: 10px;">View</a>
                    <a href="{{ route('admin.boards.edit', $board) }}" style="color: #34345C; margin-right: 10px;">Edit</a>
                    <form action="{{ route('admin.boards.destroy', $board) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this board and all its threads?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: #D00; cursor: pointer; padding: 0; font-size: inherit;">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px; color: #666;">No boards created yet. <a href="{{ route('admin.boards.create') }}">Create one now</a>.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 30px;">
    <h3>System Information</h3>
    <table>
        <tr>
            <th>Total Boards</th>
            <td>{{ $boards->count() }}</td>
        </tr>
        <tr>
            <th>Total Threads</th>
            <td>{{ $boards->sum('threads_count') }}</td>
        </tr>
        <tr>
            <th>Logged in as</th>
            <td>{{ auth('admin')->user()->username }}</td>
        </tr>
    </table>
</div>
@endsection
