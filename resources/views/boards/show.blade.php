@extends('layouts.app')

@section('title', '/' . $board->slug . '/ - ' . $board->name)

@section('content')
<h2 style="color: #AF0A0F;">/{{ $board->slug }}/ - {{ $board->name }}</h2>
<p>{{ $board->description }}</p>

<div style="margin: 20px 0;">
    <a href="{{ route('threads.create', $board) }}" style="background: #D6DAF0; border: 1px solid #B7C5D9; padding: 10px 20px; text-decoration: none; color: #000; display: inline-block;">Start a New Thread</a>
    <a href="{{ route('boards.catalog', $board) }}" style="background: #D6DAF0; border: 1px solid #B7C5D9; padding: 10px 20px; text-decoration: none; color: #000; display: inline-block; margin-left: 10px;">Catalog</a>
</div>

@forelse($threads as $thread)
    <div class="thread {{ $thread->is_pinned ? 'pinned' : '' }} {{ $thread->is_locked ? 'locked' : '' }}">
        <div style="margin-bottom: 10px;">
            <strong style="color: #AF0A0F;">{{ $thread->subject }}</strong>
            @if($thread->is_pinned)
                <span style="color: #D90; font-weight: bold;">[Pinned]</span>
            @endif
            @if($thread->is_locked)
                <span style="color: #D00; font-weight: bold;">[Locked]</span>
            @endif
            <span style="color: #666; font-size: 12px;">
                R: {{ $thread->reply_count }} / I: {{ $thread->image_count }}
            </span>
        </div>

        @foreach($thread->posts->take(5) as $index => $post)
            <div class="post {{ $index === 0 ? 'op' : '' }}" id="post-{{ $post->post_number }}">
                <div class="post-header">
                    <span class="name">{{ $post->name }}</span>
                    <span class="date">{{ $post->created_at->format('m/d/y(D)H:i:s') }}</span>
                    <span class="post-num" data-post-num="{{ $post->post_number }}">No. {{ $post->post_number }}</span>
                </div>

                @if($post->hasImage())
                    <div class="post-image">
                        <a href="{{ $post->image_url }}" target="_blank">
                            <img src="{{ $post->thumbnail_url }}" alt="Image" data-full-image="{{ $post->image_url }}">
                        </a>
                    </div>
                @endif

                <div class="post-content">
                    {!! $post->formatted_content !!}
                </div>
                <div style="clear: both;"></div>

                @auth('admin')
                    <div class="admin-actions">
                        <form action="{{ route('admin.posts.delete', [$board, $thread, $post]) }}" method="POST" onsubmit="return confirm('Delete this post?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete Post</button>
                        </form>
                    </div>
                @endauth
            </div>
        @endforeach

        @if($thread->reply_count > 4)
            <p style="margin: 10px 0;">
                <a href="{{ route('threads.show', [$board, $thread]) }}">{{ $thread->reply_count - 4 }} more replies</a>
            </p>
        @endif

        <p>
            <a href="{{ route('threads.show', [$board, $thread]) }}" style="color: #34345C; font-weight: bold;">Reply to thread</a>

            @auth('admin')
                <span class="admin-actions" style="margin-left: 20px;">
                    <form action="{{ route('admin.threads.pin', [$board, $thread]) }}" method="POST">
                        @csrf
                        <button type="submit">{{ $thread->is_pinned ? 'Unpin' : 'Pin' }}</button>
                    </form>
                    <form action="{{ route('admin.threads.lock', [$board, $thread]) }}" method="POST">
                        @csrf
                        <button type="submit">{{ $thread->is_locked ? 'Unlock' : 'Lock' }}</button>
                    </form>
                    <form action="{{ route('admin.threads.delete', [$board, $thread]) }}" method="POST" onsubmit="return confirm('Delete this thread?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete Thread</button>
                    </form>
                </span>
            @endauth
        </p>
    </div>
@empty
    <p>No threads yet. Be the first to post!</p>
@endforelse

<div class="pagination">
    {{ $threads->links() }}
</div>
@endsection
