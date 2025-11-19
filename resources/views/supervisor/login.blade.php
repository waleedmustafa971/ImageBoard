@extends('layouts.app')

@section('title', 'Supervisor Login - ImageBoard')

@section('content')
<div style="max-width: 400px; margin: 50px auto;">
    <h2 style="text-align: center; color: #0F7A0F; margin-bottom: 20px;">Supervisor Login</h2>

    <form action="{{ route('supervisor.login') }}" method="POST">
        @csrf

        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('boards.index') }}">Back to Boards</a>
    </div>
</div>
@endsection
