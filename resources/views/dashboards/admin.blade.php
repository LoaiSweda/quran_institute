<!-- resources/views/dashboards/admin.blade.php -->
@extends('layouts.app')

@section('title','لوحة تحكم المشرف')

@section('content')
<div class="auth-card">
    <h2>مرحباً، {{ auth()->user()->admin->first_name ?? auth()->user()->email }}</h2>

    <p>هذه لوحة التحكم الخاصة بالمشرف. يمكنك من هنا إدارة محتوى النظام والإعدادات.</p>

    <div style="margin-top: 30px; text-align: center;">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-submit" style="background: #a00;">
                تسجيل الخروج
            </button>
        </form>
    </div>
</div>
@endsection
