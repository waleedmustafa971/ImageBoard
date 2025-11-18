@extends('layouts.app')

@section('title', 'Manage Boards - Admin - ImageBoard')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.dashboard') }}">&larr; Back to Dashboard</a>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="color: #AF0A0F;">Manage Boards</h2>
    <a href="{{ route('admin.boards.create') }}" style="padding: 10px 20px; background: #D6DAF0; border: 1px solid #B7C5D9; text-decoration: none; color: #000;">Create New Board</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Slug</th>
            <th>Description</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($boards as $board)
        <tr>
            <td>{{ $board->id }}</td>
            <td>{{ $board->name }}</td>
            <td>/{{ $board->slug }}/</td>
            <td>{{ Str::limit($board->description, 50) }}</td>
            <td>{{ $board->created_at->format('Y-m-d') }}</td>
            <td>
                <a href="{{ route('boards.show', $board) }}" style="color: #34345C; margin-right: 10px;">View</a>
                <a href="{{ route('admin.boards.edit', $board) }}" style="color: #34345C; margin-right: 10px;">Edit</a>
                <form action="{{ route('admin.boards.destroy', $board) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this board and all its threads? This cannot be undone!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; color: #D00; cursor: pointer; padding: 0; font-size: inherit;">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align: center; padding: 20px; color: #666;">No boards found. <a href="{{ route('admin.boards.create') }}">Create one now</a>.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
