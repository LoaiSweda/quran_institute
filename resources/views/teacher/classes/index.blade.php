@extends('layouts.app')

@section('title','حلقاتي')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">حلقاتي</h1>
        {{-- إذا تريد زر إضافة حلقة جديدة --}}
        {{-- <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> إضافة حلقة
        </a> --}}
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <form method="GET" action="{{ route('teacher.classes.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input
                        type="text"
                        name="search"
                        class="form-control form-control-sm"
                        placeholder="🔍 بحث بالحلقات..."
                        value="{{ request('search') }}"
                    >
                </div>
                <div class="col-md-2 text-md-end">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-funnel-fill"></i> تطبيق
                    </button>
                    <a href="{{ route('teacher.classes.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> إعادة ضبط
                    </a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                @if($classes->isEmpty())
                    <p class="text-center text-muted py-4 mb-0">لا توجد حلقات لعرضها.</p>
                @else
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>اسم الحلقة</th>
                                <th>عدد الطلاب</th>
                                <th>عدد الجلسات</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classes as $idx => $class)
                                <tr>
                                    <td>{{ $classes->firstItem() + $idx }}</td>
                                    <td>
                                        <a href="{{ route('teacher.classes.show', $class) }}">
                                            {{ $class->name }}
                                        </a>
                                    </td>
                                    <td>{{ $class->users()->count() }}</td>
                                    <td>{{ $class->session_count }}</td>
                                    <td class="text-center">
                                        {{-- عرض تفاصيل الحلقة --}}
                                        <a href="{{ route('teacher.classes.show', $class) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="عرض">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        {{-- عرض طلاب الحلقة --}}
                                        <a href="{{ route('teacher.classes.students', $class) }}"
                                        class="btn btn-sm btn-outline-info"
                                        title="طلاب الحلقة">
                                            <i class="bi bi-people-fill"></i>
                                        </a>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        @if($classes->hasPages())
            <div class="card-footer">
                {{ $classes->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
