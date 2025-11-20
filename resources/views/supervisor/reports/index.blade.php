@extends('layouts.app')

@section('title', 'Manage Reports - Supervisor - ImageBoard')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('supervisor.dashboard') }}">&larr; Back to Dashboard</a>
</div>

<h2 style="color: #0F7A0F; margin-bottom: 20px;">Post Reports</h2>

<div style="background: #F0E0D6; padding: 15px; border: 1px solid #D9BFB7; margin-bottom: 20px;">
    <h3 style="margin-top: 0;">Filters</h3>
    <form action="{{ route('supervisor.reports.index') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: end;">
        <div>
            <label for="status" style="display: block; margin-bottom: 5px;">Status</label>
            <select id="status" name="status" style="padding: 5px;">
                <option value="">Pending (Default)</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
            </select>
        </div>

        <div>
            <button type="submit" style="padding: 5px 15px; background: #D6DAF0; border: 1px solid #B7C5D9; cursor: pointer;">Apply Filters</button>
            <a href="{{ route('supervisor.reports.index') }}" style="display: inline-block; padding: 5px 15px; background: #EEE; border: 1px solid #CCC; text-decoration: none; color: #000;">Clear</a>
        </div>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Post</th>
            <th>Board</th>
            <th>Reason</th>
            <th>Reporter IP</th>
            <th>Status</th>
            <th>Reported</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reports as $report)
        <tr>
            <td>
                <a href="{{ route('threads.show', [$report->post->thread->board, $report->post->thread]) }}#post-{{ $report->post->post_number }}" target="_blank">
                    Post #{{ $report->post->post_number }}
                </a>
                <br>
                <small>{{ Str::limit($report->post->content, 50) }}</small>
            </td>
            <td>/{{ $report->post->thread->board->slug }}/</td>
            <td>{{ ucfirst(str_replace('_', ' ', $report->reason)) }}</td>
            <td><code>{{ $report->reporter_ip }}</code></td>
            <td>
                @if($report->status === 'pending')
                    <span style="color: #F60; font-weight: bold;">Pending</span>
                @elseif($report->status === 'reviewed')
                    <span style="color: green;">Reviewed</span>
                @else
                    <span style="color: #999;">Dismissed</span>
                @endif
            </td>
            <td>{{ $report->created_at->diffForHumans() }}</td>
            <td>
                @if($report->status === 'pending')
                    <form action="{{ route('supervisor.reports.review', $report) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: #D6DAF0; border: 1px solid #B7C5D9; padding: 5px 10px; cursor: pointer; margin-right: 5px;">Mark Reviewed</button>
                    </form>
                    <form action="{{ route('supervisor.reports.dismiss', $report) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: #EEE; border: 1px solid #CCC; padding: 5px 10px; cursor: pointer;">Dismiss</button>
                    </form>
                @else
                    -
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align: center; padding: 20px; color: #666;">No reports found for your boards.</td>
        </tr>
        @endforelse
    </tbody>
</table>

@if($reports->hasPages())
<div style="margin-top: 20px; text-align: center;">
    {{ $reports->links() }}
</div>
@endif
@endsection
