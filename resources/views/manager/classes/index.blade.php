@extends('layouts.app')
@section('title','إدارة الحلقات')

@section('content')
    <div class="container-fluid">

        {{-- Header + Add --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">إدارة الحلقات</h1>
            <a href="{{ route('manager.classes.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> إضافة حلقة جديدة
            </a>
        </div>

        {{-- Table --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 text-end">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم الحلقة</th>
                            <th>المادة</th>
                            <th>المدرّس</th>
                            <th>عدد الطلاب</th>
                            <th>نسبة الحضور</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($classes as $cls)
                            <tr>
                                <td>{{ $loop->iteration + ($classes->currentPage()-1)*$classes->perPage() }}</td>
                                <td>{{ $cls->name }}</td>
                                <td>{{ optional($cls->subject)->name }}</td>
                                <td>
                                    {{-- الاسم الأول + الكنية --}}
                                    {{ optional($cls->teacher)->first_name }}
                                    {{ optional($cls->teacher)->last_name }}
                                </td>
                                <td>{{ $cls->students_count }}</td>
                                <td>{{ number_format($cls->present_percentage,1) }}%</td>
                                <td class="text-center">
                                    <a href="{{ route('manager.classes.show',$cls) }}"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('manager.classes.edit',$cls) }}"
                                       class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('manager.classes.destroy',$cls) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('هل تريد حذف الحلقة؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">لا توجد حلقات</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
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
