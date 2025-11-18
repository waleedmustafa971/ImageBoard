@extends('layouts.app')

@section('title', 'Start a New Thread - /' . $board->slug . '/')

@section('content')
<h2 style="color: #AF0A0F;">Start a New Thread - /{{ $board->slug }}/</h2>

<div style="margin: 20px 0;">
    <a href="{{ route('boards.show', $board) }}" style="color: #34345C;">&larr; Back to Board</a>
</div>

<form action="{{ route('threads.store', $board) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label for="name">Name (optional):</label>
    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Anonymous">

    <label for="subject">Subject: <span style="color: red;">*</span></label>
    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required maxlength="100">

    <label for="content">Comment: <span style="color: red;">*</span></label>
    <textarea name="content" id="content" required maxlength="2000">{{ old('content') }}</textarea>

    <label for="image">Image: <span style="color: red;">*</span></label>
    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif,image/webp" required>
    <small style="display: block; margin-top: 5px; color: #666;">Max 5MB. Allowed: JPG, PNG, GIF, WEBP</small>

    <button type="submit">Create Thread</button>
</form>
@endsection
