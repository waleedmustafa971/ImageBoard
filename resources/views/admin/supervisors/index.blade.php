@extends('layouts.app')

@section('title', 'Manage Supervisors - Admin - ImageBoard')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.dashboard') }}">&larr; Back to Dashboard</a>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="color: #AF0A0F;">Manage Supervisors</h2>
    <a href="{{ route('admin.supervisors.create') }}" style="padding: 10px 20px; background: #D6DAF0; border: 1px solid #B7C5D9; text-decoration: none; color: #000;">Create New Supervisor</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Assigned Boards</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($supervisors as $supervisor)
        <tr>
            <td>{{ $supervisor->id }}</td>
            <td>{{ $supervisor->username }}</td>
            <td>{{ $supervisor->email }}</td>
            <td>{{ $supervisor->boards_count }}</td>
            <td>
                @if($supervisor->is_active)
                    <span style="color: green;">Active</span>
                @else
                    <span style="color: red;">Inactive</span>
                @endif
            </td>
            <td>{{ $supervisor->created_at->format('Y-m-d') }}</td>
            <td>
                <a href="{{ route('admin.supervisors.edit', $supervisor) }}" style="color: #34345C; margin-right: 10px;">Edit</a>
                <form action="{{ route('admin.supervisors.destroy', $supervisor) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this supervisor? This cannot be undone!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; color: #D00; cursor: pointer; padding: 0; font-size: inherit;">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align: center; padding: 20px; color: #666;">No supervisors found. <a href="{{ route('admin.supervisors.create') }}">Create one now</a>.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
