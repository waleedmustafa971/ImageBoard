@extends('layouts.app')

@section('title', 'Create Ban - Admin - ImageBoard')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.bans.index') }}">&larr; Back to Bans</a>
</div>

<h2 style="color: #AF0A0F; margin-bottom: 20px;">Create New Ban</h2>

<form action="{{ route('admin.bans.store') }}" method="POST">
    @csrf

    <label for="ip_address">IP Address <span style="color: red;">*</span></label>
    <input type="text" id="ip_address" name="ip_address" value="{{ old('ip_address') }}" required placeholder="e.g., 192.168.1.1 or 2001:db8::1">
    <small>The IP address to ban (IPv4 or IPv6)</small>

    <label for="board_id">Board Scope</label>
    <select id="board_id" name="board_id">
        <option value="">Global Ban (All Boards)</option>
        @foreach($boards as $board)
            <option value="{{ $board->id }}" {{ old('board_id') == $board->id ? 'selected' : '' }}>
                /{{ $board->slug }}/ - {{ $board->name }}
            </option>
        @endforeach
    </select>
    <small>Leave as "Global Ban" to ban from all boards, or select a specific board</small>

    <label for="reason">Reason <span style="color: red;">*</span></label>
    <textarea id="reason" name="reason" rows="4" required placeholder="e.g., Spam posting, rule violation, etc.">{{ old('reason') }}</textarea>
    <small>Explain why this IP is being banned</small>

    <label for="duration">Duration <span style="color: red;">*</span></label>
    <select id="duration" name="duration" required>
        <option value="1hour" {{ old('duration') == '1hour' ? 'selected' : '' }}>1 Hour</option>
        <option value="1day" {{ old('duration', '1day') == '1day' ? 'selected' : '' }}>1 Day</option>
        <option value="1week" {{ old('duration') == '1week' ? 'selected' : '' }}>1 Week</option>
        <option value="1month" {{ old('duration') == '1month' ? 'selected' : '' }}>1 Month</option>
        <option value="permanent" {{ old('duration') == 'permanent' ? 'selected' : '' }}>Permanent</option>
    </select>

    <div style="margin-top: 20px;">
        <button type="submit">Create Ban</button>
        <a href="{{ route('admin.bans.index') }}" style="display: inline-block; padding: 8px 20px; background: #EEE; border: 1px solid #CCC; text-decoration: none; color: #000; margin-left: 10px;">Cancel</a>
    </div>
</form>
@endsection
