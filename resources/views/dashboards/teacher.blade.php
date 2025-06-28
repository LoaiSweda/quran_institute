<!-- resources/views/dashboards/teacher.blade.php -->
@extends('layouts.app')

@section('title','لوحة تحكم المعلم')

@section('content')
<div class="auth-card">
    <h2>مرحباً، {{ auth()->user()->teacher->first_name ?? auth()->user()->email }}</h2>

    <p>هذه لوحة تحكم <strong>المعلم</strong>. من هنا يمكنك:</p>

    <ul style="list-style: none; padding: 0; margin-top: 20px;">
        <li><a href="#" style="text-decoration: none; display: block; margin-bottom: 8px;">→ عرض الحلقات التي تُدَرِّسها</a></li>
        <li><a href="#" style="text-decoration: none; display: block; margin-bottom: 8px;">→ إضافة أو تعديل الواجبات</a></li>
        <li><a href="#" style="text-decoration: none; display: block; margin-bottom: 8px;">→ متابعة تقدم الطلاب</a></li>
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
