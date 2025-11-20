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

        <!-- Post Actions -->
        <span style="float: right;">
            <!-- Report Button (public) -->
            @guest('admin')
            @guest('supervisor')
            <button onclick="showReportForm({{ $post->id }})" style="padding: 2px 5px; font-size: 11px; background: #FFF; border: 1px solid #CCC; cursor: pointer; margin-right: 5px;">Report</button>
            @endguest
            @endguest

            <!-- Admin Post Actions -->
            @auth('admin')
            <form action="{{ route('admin.posts.delete', [$board, $thread, $post]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this post?')">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: 2px 5px; font-size: 11px; background: #F8D7DA; border-color: #F5C6CB;">Delete</button>
            </form>
            @endauth
        </span>

        <!-- Report Form (hidden by default) -->
        <div id="report-form-{{ $post->id }}" style="display: none; margin-top: 10px; padding: 10px; background: #FFF; border: 1px solid #CCC;">
            <form action="{{ route('posts.report', [$board, $thread, $post]) }}" method="POST">
                @csrf
                <strong>Report this post:</strong><br>
                <select name="reason" required style="margin: 10px 0; padding: 5px;">
                    <option value="">Select a reason...</option>
                    <option value="spam">Spam</option>
                    <option value="illegal">Illegal Content</option>
                    <option value="harassment">Harassment</option>
                    <option value="off_topic">Off Topic</option>
                    <option value="other">Other</option>
                </select><br>
                <button type="submit" style="padding: 5px 10px; background: #D6DAF0; border: 1px solid #B7C5D9; cursor: pointer;">Submit Report</button>
                <button type="button" onclick="hideReportForm({{ $post->id }})" style="padding: 5px 10px; background: #EEE; border: 1px solid #CCC; cursor: pointer;">Cancel</button>
            </form>
        </div>
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

    @if($post->images->count() > 0)
    <div class="post-images" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0;">
        @foreach($post->images as $image)
        <div class="post-image-item">
            <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank">
                <img src="{{ asset('storage/' . $image->thumbnail_path) }}"
                     alt="Post image {{ $loop->iteration }}"
                     style="max-width: 150px; max-height: 150px;">
            </a>
        </div>
        @endforeach
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

        <label for="images">Images (optional - up to 4)</label>
        <input type="file" id="images" name="images[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
        <small>Upload up to 4 images. Max 5MB each. JPG, PNG, GIF, WEBP.</small>

        @php
            $captcha = \App\Helpers\Captcha::generate();
        @endphp
        <label for="captcha">Verification: <span style="color: red;">*</span></label>
        <div style="margin-bottom: 10px;">
            <strong style="font-size: 18px; color: #AF0A0F;">{{ $captcha['question'] }}</strong>
        </div>
        <input type="number" name="captcha" id="captcha" required placeholder="Enter your answer">
        <small style="display: block; margin-top: 5px; color: #666;">Solve the math problem to verify you're human</small>

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

    // Report form functions
    function showReportForm(postId) {
        document.getElementById('report-form-' + postId).style.display = 'block';
    }

    function hideReportForm(postId) {
        document.getElementById('report-form-' + postId).style.display = 'none';
    }

    // Live thread updates
    let lastPostId = {{ $thread->posts->last()->id ?? 0 }};
    let updateInterval = 10000; // Check every 10 seconds

    function checkForNewPosts() {
        fetch('{{ route('threads.newPosts', [$board, $thread]) }}?last_post_id=' + lastPostId)
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    data.posts.forEach(post => {
                        addPostToThread(post);
                        lastPostId = Math.max(lastPostId, post.id);
                    });
                }
            })
            .catch(error => console.log('Update check failed:', error));
    }

    function addPostToThread(post) {
        const postDiv = document.createElement('div');
        postDiv.className = 'post';
        postDiv.id = 'post-' + post.post_number;

        let imageHtml = '';
        if (post.image_path) {
            imageHtml = `
                <div class="post-image">
                    <div>
                        <a href="/storage/${post.image_path}" target="_blank">
                            <img src="/storage/${post.image_thumbnail_path}" alt="Post image">
                        </a>
                    </div>
                </div>
            `;
        }

        if (post.images && post.images.length > 0) {
            imageHtml += '<div class="post-images" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0;">';
            post.images.forEach(image => {
                imageHtml += `
                    <div class="post-image-item">
                        <a href="/storage/${image.image_path}" target="_blank">
                            <img src="/storage/${image.thumbnail_path}" alt="Post image" style="max-width: 150px; max-height: 150px;">
                        </a>
                    </div>
                `;
            });
            imageHtml += '</div>';
        }

        postDiv.innerHTML = `
            <div class="post-header">
                <span class="name">${post.name}</span>
                <span class="date">${post.created_at}</span>
                <span class="post-num" data-post-num="${post.post_number}">No. ${post.post_number}</span>
                <span style="color: #0F7A0F; font-weight: bold; margin-left: 10px;">NEW</span>
            </div>
            ${imageHtml}
            <div class="post-content">${post.content.replace(/\n/g, '<br>')}</div>
            <div style="clear: both;"></div>
        `;

        // Insert before reply form
        const replyForm = document.querySelector('div[style*="margin-top: 30px"] h3');
        if (replyForm && replyForm.textContent === 'Post a Reply') {
            replyForm.parentElement.insertAdjacentElement('beforebegin', postDiv);
        }

        // Process quote links in new post
        processQuoteLinks(postDiv);
    }

    function processQuoteLinks(element) {
        const content = element.querySelector('.post-content');
        if (content) {
            content.innerHTML = content.innerHTML.replace(/&gt;&gt;(\d+)/g, '<a href="#post-$1" class="quote-link">&gt;&gt;$1</a>');
        }
    }

    // Start live updates
    setInterval(checkForNewPosts, updateInterval);
</script>
@endsection
