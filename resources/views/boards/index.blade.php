@extends('layouts.app')

@section('title', 'Boards - ImageBoard')

@section('content')
<h2 style="margin-bottom: 20px; color: #AF0A0F;">Boards</h2>

<div class="board-list">
    @forelse($boards as $board)
        <div class="board-card">
            <h3>
                <a href="{{ route('boards.show', $board) }}" style="text-decoration: none; color: #AF0A0F;">
                    /{{ $board->slug }}/ - {{ $board->name }}
                </a>
            </h3>
            <p>{{ $board->description }}</p>
            <div class="meta">
                @if($board->is_nsfw)
                    <span style="color: #D00; font-weight: bold;">[NSFW]</span>
                @endif
                Posts: {{ $board->post_count }}
            </div>
            <div style="margin-top: 10px;">
                <a href="{{ route('boards.catalog', $board) }}" style="color: #34345C;">Catalog</a>
            </div>
        </div>
    @empty
        <p>No boards available. Please contact the administrator.</p>
    @endforelse
</div>
@endsection
