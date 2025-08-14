@extends('layouts.app')
@section('title','إدارة الحلقات')

@section('content')
    <div class="container-fluid">

        {{-- Header + Add --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">إدارة الحلقات</h1>
            <a href="{{ route('admin.classes.create', ['institute_id'=>$inst->id]) }}" class="btn btn-primary btn-sm">
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
                                    {{ optional($cls->teacher)->first_name }}
                                    {{ optional($cls->teacher)->last_name }}
                                </td>
                                <td>{{ $cls->students_count }}</td>
                                <td>{{ number_format($cls->present_percentage,1) }}%</td>
                                <td class="text-center">

                                    {{-- زر الانتقال لصفحة إضافة موعد (للمشرف إذا وفّرت مسارات المواعيد) --}}
                                    <a href="{{ route('admin.classes.schedules.create', ['class'=>$cls->id, 'institute_id'=>$inst->id]) }}"
                                       class="btn btn-sm btn-outline-success"
                                       title="إضافة موعد للحلقة">
                                        <i class="bi bi-calendar-plus"></i>
                                    </a>

                                    {{-- Show --}}
                                    <a href="{{ route('admin.classes.show', ['class'=>$cls->id, 'institute_id'=>$inst->id]) }}"
                                       class="btn btn-sm btn-outline-info"
                                       title="عرض التفاصيل">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.classes.edit', ['class'=>$cls->id, 'institute_id'=>$inst->id]) }}"
                                       class="btn btn-sm btn-outline-warning"
                                       title="تعديل الحلقة">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.classes.destroy', ['class'=>$cls->id, 'institute_id'=>$inst->id]) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('هل تريد حذف الحلقة؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="حذف الحلقة">
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
                    {{ $classes->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

    </div>
@endsection
