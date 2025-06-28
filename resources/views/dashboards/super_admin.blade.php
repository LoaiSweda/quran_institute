<!-- resources/views/dashboards/super_admin.blade.php -->
@extends('layouts.app')

@section('title','لوحة تحكم المالك')

@section('content')
<div class="auth-card">
    <h2>مرحباً، {{ auth()->user()->admin->first_name ?? auth()->user()->email }}</h2>

    <p>هذه لوحة تحكم الـ <strong>Super Admin</strong>. يمكنك من هنا إدارة جميع إعدادات النظام والأدوار.</p>

    <ul style="list-style: none; padding: 0; margin-top: 20px;">
        <li><a href="#" style="text-decoration: none; display: block; margin-bottom: 8px;">→ إدارة المشرفين</a></li>
        <li><a href="#" style="text-decoration: none; display: block; margin-bottom: 8px;">→ إدارة مديري المعاهد</a></li>
        <li><a href="#" style="text-decoration: none; display: block; margin-bottom: 8px;">→ إعدادات النظام العامة</a></li>
    </ul>

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
