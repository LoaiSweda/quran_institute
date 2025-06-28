@extends('layouts.auth')


@section('title','تسجيل الدخول')

@section('content')
    <h2>تسجيل الدخول</h2>
    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}" required autofocus>
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <input id="password" type="password" name="password" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="remember-forgot">
            <label><input type="checkbox" name="remember"> تذكرني</label>
            <a href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
        </div>

        <button type="submit" class="btn-submit">دخول</button>
    </form>
@endsection
