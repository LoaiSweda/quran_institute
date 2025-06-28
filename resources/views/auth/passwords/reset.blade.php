@extends('layouts.auth')

@section('title','إعادة تعيين كلمة المرور')

@section('content')
<div class="auth-card">
    <h2>إعادة تعيين كلمة المرور</h2>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input id="email" name="email" type="email"
                   value="{{ $email ?? old('email') }}" required autofocus>
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">كلمة المرور الجديدة</label>
            <input id="password" name="password" type="password" required>
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">تأكيد كلمة المرور</label>
            <input id="password_confirmation"
                   name="password_confirmation" type="password" required>
        </div>

        <button type="submit" class="btn-submit">
            إعادة التعيين
        </button>
    </form>
</div>
@endsection
