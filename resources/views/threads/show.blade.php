@extends('layouts.app')

@section('title', $thread->subject . ' - /' . $board->slug . '/ - ImageBoard')

@section('content')
<div style="margin-bottom: 20px;">
    <nav>
        <a href="{{ route('boards.index') }}">Home</a> /
        <a href="{{ route('boards.show', $board) }}">{{ $board->name }}</a> /
        <a href="{{ route('boards.catalog', $board) }}">Catalog</a>
    </nav>
</div>

<div style="text-align: center; margin-bottom: 20px;">
    <h2 style="color: #AF0A0F;">/{{ $board->slug }}/ - {{ $board->name }}</h2>
    <p>{{ $board->description }}</p>
</div>

<!-- Thread Actions (Admin) -->
@auth('admin')
<div class="admin-actions">
    <form action="{{ route('admin.threads.pin', [$board, $thread]) }}" method="POST">
        @csrf
        <button type="submit">{{ $thread->is_pinned ? 'Unpin' : 'Pin' }} Thread</button>
    </form>
    <form action="{{ route('admin.threads.lock', [$board, $thread]) }}" method="POST">
        @csrf
        <button type="submit">{{ $thread->is_locked ? 'Unlock' : 'Lock' }} Thread</button>
    </form>
    <form action="{{ route('admin.threads.delete', [$board, $thread]) }}" method="POST" onsubmit="return confirm('Delete this thread?')">
        @csrf
        @method('DELETE')
        <button type="submit" style="background: #F8D7DA; border-color: #F5C6CB;">Delete Thread</button>
    </form>
</div>
@endauth

<!-- Thread Posts -->
@foreach($thread->posts as $index => $post)
<div class="post {{ $index === 0 ? 'op' : '' }}" id="post-{{ $post->post_number }}">
    <div class="post-header">
        <span class="name">{{ $post->name }}</span>
        <span class="date">{{ $post->created_at->format('m/d/y(D)H:i:s') }}</span>
        <span class="post-num" data-post-num="{{ $post->post_number }}">No. {{ $post->post_number }}</span>

        @if($index === 0)
            <strong style="color: #AF0A0F;">{{ $thread->subject }}</strong>
            @if($thread->is_pinned)
                <span style="color: #F60;">📌 Pinned</span>
            @endif
            @if($thread->is_locked)
                <span style="color: #F60;">🔒 Locked</span>
            @endif
        @endif

        <!-- Admin Post Actions -->
        @auth('admin')
        <span style="float: right;">
            <form action="{{ route('admin.posts.delete', [$board, $thread, $post]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this post?')">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: 2px 5px; font-size: 11px; background: #F8D7DA; border-color: #F5C6CB;">Delete</button>
            </form>
        </span>
        @endauth
    </div>

    @if($post->image_path)
    <div class="post-image">
        <div>
            <a href="{{ asset('storage/' . $post->image_path) }}" target="_blank">
                <img src="{{ asset('storage/' . $post->image_thumbnail_path) }}"
                     alt="Post image"
                     data-full-image="{{ asset('storage/' . $post->image_path) }}">
            </a>
        </div>
    </div>
    @endif

    <div class="post-content">
        {!! nl2br(e($post->content)) !!}
    </div>
    <div style="clear: both;"></div>
</div>
@endforeach

<!-- Reply Form -->
@if(!$thread->is_locked || auth('admin')->check())
<div style="margin-top: 30px;">
    <h3>Post a Reply</h3>
    <form action="{{ route('posts.store', [$board, $thread]) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label for="name">Name (optional)</label>
        <input type="text" id="name" name="name" value="Anonymous" maxlength="50">

        <label for="content">Comment <span style="color: red;">*</span></label>
        <textarea id="content" name="content" required maxlength="2000"></textarea>

        <label for="image">Image (optional)</label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif">
        <small>Max 5MB. JPG, PNG, GIF.</small>

        <button type="submit">Post Reply</button>
    </form>
</div>
@else
<div style="margin-top: 30px; padding: 15px; background: #F8D7DA; border: 1px solid #F5C6CB; color: #721C24;">
    This thread is locked. No new replies can be posted.
</div>
@endif

<script>
    // Process quote links
    document.addEventListener('DOMContentLoaded', function() {
        const content = document.querySelectorAll('.post-content');
        content.forEach(function(el) {
            el.innerHTML = el.innerHTML.replace(/&gt;&gt;(\d+)/g, '<a href="#post-$1" class="quote-link">&gt;&gt;$1</a>');
        });
    });
</script>
@endsection
