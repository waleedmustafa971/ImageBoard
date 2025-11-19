<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ImageBoard')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #EEF2FF; color: #000; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { background: #D6DAF0; border-bottom: 1px solid #B7C5D9; padding: 10px 0; margin-bottom: 20px; }
        header .header-content { display: flex; align-items: center; justify-content: center; gap: 15px; }
        header .logo { width: 50px; height: 50px; }
        header h1 { color: #AF0A0F; font-size: 28px; margin: 0; }
        nav { text-align: center; margin: 10px 0; }
        nav a { color: #34345C; text-decoration: none; margin: 0 10px; font-weight: bold; }
        nav a:hover { text-decoration: underline; }
        .board-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .board-card { background: #D6DAF0; border: 1px solid #B7C5D9; padding: 15px; }
        .board-card h3 { color: #AF0A0F; margin-bottom: 5px; }
        .board-card .meta { color: #666; font-size: 12px; }
        .thread { background: #D6DAF0; border: 1px solid #B7C5D9; margin-bottom: 10px; padding: 10px; }
        .thread.pinned { background: #FFE; border-color: #DD6; }
        .thread.locked::before { content: "🔒 "; }
        .post { background: #D6DAF0; border: 1px solid #B7C5D9; margin: 5px 0; padding: 10px; }
        .post.op { background: #D9E0F2; }
        .post-header { margin-bottom: 10px; font-weight: bold; color: #117743; }
        .post-header .name { color: #117743; }
        .post-header .post-num { color: #000; }
        .post-header .date { color: #666; font-size: 12px; }
        .post-content { margin: 10px 0; }
        .post-image { float: left; margin: 0 10px 10px 0; }
        .post-image img { max-width: 250px; cursor: pointer; border: 1px solid #B7C5D9; }
        .quote-link { color: #D00; text-decoration: none; }
        .quote-link:hover { text-decoration: underline; }
        form { background: #D6DAF0; border: 1px solid #B7C5D9; padding: 15px; margin: 20px 0; }
        form label { display: block; margin: 10px 0 5px; font-weight: bold; }
        form input[type="text"], form textarea, form input[type="file"] { width: 100%; padding: 8px; border: 1px solid #B7C5D9; }
        form textarea { min-height: 100px; font-family: inherit; }
        form button { background: #D6DAF0; border: 1px solid #B7C5D9; padding: 8px 20px; cursor: pointer; margin-top: 10px; }
        form button:hover { background: #C5CEE0; }
        .catalog { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
        .catalog-item { background: #D6DAF0; border: 1px solid #B7C5D9; padding: 10px; text-align: center; }
        .catalog-item img { max-width: 100%; margin-bottom: 10px; }
        .alert { padding: 10px; margin: 10px 0; border: 1px solid; }
        .alert-success { background: #D4EDDA; border-color: #C3E6CB; color: #155724; }
        .alert-error { background: #F8D7DA; border-color: #F5C6CB; color: #721C24; }
        .admin-actions { margin: 10px 0; }
        .admin-actions form { display: inline; margin: 0; padding: 0; background: none; border: none; }
        .admin-actions button { padding: 5px 10px; margin: 0 5px; font-size: 12px; }
        .pagination { text-align: center; margin: 20px 0; }
        .pagination a, .pagination span { display: inline-block; padding: 5px 10px; margin: 0 2px; background: #D6DAF0; border: 1px solid #B7C5D9; text-decoration: none; color: #000; }
        .pagination .active { background: #AF0A0F; color: #FFF; }
        table { width: 100%; background: #D6DAF0; border-collapse: collapse; }
        table th, table td { padding: 10px; border: 1px solid #B7C5D9; text-align: left; }
        table th { background: #C5CEE0; font-weight: bold; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <img src="{{ asset('images/logo.svg') }}" alt="ImageBoard Logo" class="logo">
            <h1>ImageBoard</h1>
        </div>
        <nav>
            <a href="{{ route('boards.index') }}">Home</a>
            @auth('admin')
                <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #34345C; cursor: pointer; font-weight: bold;">Logout</button>
                </form>
            @elseauth('supervisor')
                <a href="{{ route('supervisor.dashboard') }}">Supervisor Dashboard</a>
                <form action="{{ route('supervisor.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #34345C; cursor: pointer; font-weight: bold;">Logout</button>
                </form>
            @else
                <a href="{{ route('admin.login') }}">Admin Login</a>
                <a href="{{ route('supervisor.login') }}">Supervisor Login</a>
            @endauth
        </nav>
    </header>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script>
        // Click on post number to quote
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.post-num').forEach(function(el) {
                el.style.cursor = 'pointer';
                el.addEventListener('click', function() {
                    const postNum = this.dataset.postNum;
                    const replyForm = document.querySelector('textarea[name="content"]');
                    if (replyForm) {
                        replyForm.value += '>>' + postNum + '\n';
                        replyForm.focus();
                    }
                });
            });

            // Image lightbox
            document.querySelectorAll('.post-image img').forEach(function(img) {
                img.addEventListener('click', function() {
                    const fullImage = this.dataset.fullImage;
                    if (fullImage) {
                        window.open(fullImage, '_blank');
                    }
                });
            });
        });
    </script>
</body>
</html>
