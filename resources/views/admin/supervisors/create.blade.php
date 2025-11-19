@extends('layouts.app')

@section('title', 'Create Supervisor - Admin - ImageBoard')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.supervisors.index') }}">&larr; Back to Supervisors</a>
</div>

<h2 style="color: #AF0A0F; margin-bottom: 20px;">Create New Supervisor</h2>

<form action="{{ route('admin.supervisors.store') }}" method="POST">
    @csrf

    <label for="username">Username <span style="color: red;">*</span></label>
    <input type="text" id="username" name="username" value="{{ old('username') }}" required maxlength="255" placeholder="e.g., john_doe">

    <label for="email">Email <span style="color: red;">*</span></label>
    <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="255" placeholder="e.g., john@example.com">

    <label for="password">Password <span style="color: red;">*</span></label>
    <input type="password" id="password" name="password" required minlength="8" placeholder="Minimum 8 characters">

    <label for="password_confirmation">Confirm Password <span style="color: red;">*</span></label>
    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder="Re-enter password">

    <label>
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
        Active (Allow this supervisor to login)
    </label>

    <div style="margin-top: 20px;">
        <h3>Assign to Boards</h3>
        <small>Select which boards this supervisor can moderate:</small>
        <div style="margin-top: 10px;">
            @forelse($boards as $board)
                <label style="display: block; margin: 5px 0;">
                    <input type="checkbox" name="boards[]" value="{{ $board->id }}" {{ in_array($board->id, old('boards', [])) ? 'checked' : '' }}>
                    /{{ $board->slug }}/ - {{ $board->name }}
                </label>
            @empty
                <p style="color: #666;">No boards available. <a href="{{ route('admin.boards.create') }}">Create a board first</a>.</p>
            @endforelse
        </div>
    </div>

    <div style="margin-top: 20px;">
        <button type="submit">Create Supervisor</button>
        <a href="{{ route('admin.supervisors.index') }}" style="display: inline-block; padding: 8px 20px; background: #EEE; border: 1px solid #CCC; text-decoration: none; color: #000; margin-left: 10px;">Cancel</a>
    </div>
</form>
@endsection
