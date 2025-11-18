@extends('layouts.app')

@section('title', 'Create Board - Admin - ImageBoard')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.boards.index') }}">&larr; Back to Boards</a>
</div>

<h2 style="color: #AF0A0F; margin-bottom: 20px;">Create New Board</h2>

<form action="{{ route('admin.boards.store') }}" method="POST">
    @csrf

    <label for="name">Board Name <span style="color: red;">*</span></label>
    <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="50" placeholder="e.g., Technology">

    <label for="slug">Board Slug <span style="color: red;">*</span></label>
    <input type="text" id="slug" name="slug" value="{{ old('slug') }}" required maxlength="10" placeholder="e.g., tech" pattern="[a-z0-9]+" title="Only lowercase letters and numbers">
    <small>Only lowercase letters and numbers. This will be used in URLs: /slug/</small>

    <label for="description">Description <span style="color: red;">*</span></label>
    <textarea id="description" name="description" required maxlength="200" placeholder="Brief description of what this board is about">{{ old('description') }}</textarea>

    <button type="submit">Create Board</button>
    <a href="{{ route('admin.boards.index') }}" style="display: inline-block; padding: 8px 20px; background: #EEE; border: 1px solid #CCC; text-decoration: none; color: #000; margin-left: 10px;">Cancel</a>
</form>

<script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function(e) {
        const slug = document.getElementById('slug');
        if (!slug.dataset.manuallyEdited) {
            slug.value = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9]/g, '')
                .substring(0, 10);
        }
    });

    document.getElementById('slug').addEventListener('input', function() {
        this.dataset.manuallyEdited = 'true';
    });
</script>
@endsection
