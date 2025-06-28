@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>تفاصيل المعهد: {{ $institute->name }}</h1>
        <p><strong>العنوان:</strong> {{ $institute->address }}</p>
        <p><strong>المدير:</strong> {{ $institute->manager->email }}</p>
        @if($institute->image)
            <p><img src="{{ asset('storage/'.$institute->image) }}" width="150"></p>
        @endif

        <h3>إحصائيات</h3>
        <ul>
            <li>عدد المواد: {{ $stats['subjects_count'] }}</li>
            <li>عدد الحلقات: {{ $stats['classes_count'] }}</li>
            <li>عدد المشرفين الإضافيين: {{ $stats['admins_count'] }}</li>
        </ul>

        <a href="{{ route('super-admin.institutes.index') }}">« رجوع للقائمة</a>
    </div>
@endsection
