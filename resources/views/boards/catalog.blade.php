@extends('layouts.app')

@section('title', 'Catalog - /' . $board->slug . '/')

@section('content')
<h2 style="color: #AF0A0F;">Catalog - /{{ $board->slug }}/ - {{ $board->name }}</h2>

<div style="margin: 20px 0;">
    <a href="{{ route('boards.show', $board) }}" style="background: #D6DAF0; border: 1px solid #B7C5D9; padding: 10px 20px; text-decoration: none; color: #000; display: inline-block;">Back to Board</a>
    <a href="{{ route('threads.create', $board) }}" style="background: #D6DAF0; border: 1px solid #B7C5D9; padding: 10px 20px; text-decoration: none; color: #000; display: inline-block; margin-left: 10px;">Start a New Thread</a>
</div>

<div class="catalog">
    @forelse($threads as $thread)
        @php
            $op = $thread->posts->first();
        @endphp
        <div class="catalog-item">
            @if($op && $op->hasImage())
                <a href="{{ route('threads.show', [$board, $thread]) }}">
                    <img src="{{ $op->thumbnail_url }}" alt="Thread image">
                </a>
            @endif
            <div style="margin-top: 10px;">
                <strong style="color: #AF0A0F;">
                    <a href="{{ route('threads.show', [$board, $thread]) }}" style="text-decoration: none; color: #AF0A0F;">
                        {{ Str::limit($thread->subject, 30) }}
                    </a>
                </strong>
            </div>
            <div style="font-size: 12px; color: #666;">
                R: {{ $thread->reply_count }} / I: {{ $thread->image_count }}
            </div>
            @if($op)
                <div style="font-size: 12px; margin-top: 5px;">
                    {{ Str::limit(strip_tags($op->content), 100) }}
                </div>
            @endif
        </div>
    @empty
        <p>No threads yet.</p>
    @endforelse
</div>
@endsection
