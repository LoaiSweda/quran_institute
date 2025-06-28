@extends('layouts.auth')

@section('title','استعادة كلمة المرور')

@section('content')
<div class="auth-card">
    <h2>استعادة كلمة المرور</h2>
    @if(session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input id="email" name="email" type="email"
                   value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">
            إرسال رابط الاستعادة
        </button>
    </form>
</div>
@endsection
