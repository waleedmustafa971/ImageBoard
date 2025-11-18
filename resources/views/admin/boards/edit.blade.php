@extends('layouts.app')

@section('title', 'Edit Board - Admin - ImageBoard')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.boards.index') }}">&larr; Back to Boards</a>
</div>

<h2 style="color: #AF0A0F; margin-bottom: 20px;">Edit Board: {{ $board->name }}</h2>

<form action="{{ route('admin.boards.update', $board) }}" method="POST">
    @csrf
    @method('PUT')

    <label for="name">Board Name <span style="color: red;">*</span></label>
    <input type="text" id="name" name="name" value="{{ old('name', $board->name) }}" required maxlength="50">

    <label for="slug">Board Slug <span style="color: red;">*</span></label>
    <input type="text" id="slug" name="slug" value="{{ old('slug', $board->slug) }}" required maxlength="10" pattern="[a-z0-9]+" title="Only lowercase letters and numbers">
    <small>Only lowercase letters and numbers. Warning: Changing the slug will break existing URLs!</small>

    <label for="description">Description <span style="color: red;">*</span></label>
    <textarea id="description" name="description" required maxlength="200">{{ old('description', $board->description) }}</textarea>

    <button type="submit">Update Board</button>
    <a href="{{ route('admin.boards.index') }}" style="display: inline-block; padding: 8px 20px; background: #EEE; border: 1px solid #CCC; text-decoration: none; color: #000; margin-left: 10px;">Cancel</a>
</form>

<div style="margin-top: 40px; padding: 15px; background: #F8D7DA; border: 1px solid #F5C6CB;">
    <h3 style="color: #721C24; margin-bottom: 10px;">Danger Zone</h3>
    <p style="color: #721C24; margin-bottom: 10px;">Deleting this board will permanently remove all threads and posts associated with it. This action cannot be undone!</p>
    <form action="{{ route('admin.boards.destroy', $board) }}" method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete this board and all its content?')">
        @csrf
        @method('DELETE')
        <button type="submit" style="background: #D00; color: #FFF; border-color: #A00;">Delete Board</button>
    </form>
</div>
@endsection
