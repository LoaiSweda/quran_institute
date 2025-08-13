{{-- resources/views/manager/subjects/index.blade.php --}}
@extends('layouts.app')

@section('title', 'إدارة المواد')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">إدارة المواد</h1>
            <a href="{{ route('manager.subjects.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> إضافة مادة جديدة
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <form method="GET" action="{{ route('manager.subjects.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input
                            type="text"
                            name="search"
                            class="form-control form-control-sm"
                            placeholder="🔍 بحث بالاسم أو الوصف..."
                            value="{{ request('search') }}"
                        >
                    </div>
                    <div class="col-md-3">
                        <select name="sort" class="form-select form-select-sm">
                            <option value="">فرز حسب</option>
                            <option value="name"       @selected(request('sort')=='name')>الاسم</option>
                            <option value="start_date" @selected(request('sort')=='start_date')>تاريخ البداية</option>
                            <option value="is_active"  @selected(request('sort')=='is_active')>الحالة</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="direction" class="form-select form-select-sm">
                            <option value="asc"  @selected(request('direction')=='asc')>تصاعدي</option>
                            <option value="desc" @selected(request('direction')=='desc')>تنازلي</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-funnel-fill"></i> تطبيق
                        </button>
                        <a href="{{ route('manager.subjects.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-counterclockwise"></i> إعادة ضبط
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 text-end">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>تاريخ البداية</th>
                            <th>تاريخ النهاية</th>
                            <th>الحالة</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($subjects as $subject)
                            <tr>
                                <td>{{ $loop->iteration + ($subjects->perPage() * ($subjects->currentPage() - 1)) }}</td>
                                <td>
                                    <a href="{{ route('manager.subjects.show', $subject) }}">
                                        {{ $subject->name }}
                                    </a>
                                </td>
                                <td>
                                    {{ $subject->start_date
                                        ? $subject->start_date->format('Y-m-d')
                                        : '—' }}
                                </td>
                                <td>
                                    {{ $subject->end_date
                                        ? $subject->end_date->format('Y-m-d')
                                        : '—' }}
                                </td>
                                <td>
                                    @if($subject->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-secondary">معطل</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- Show --}}
                                    <a href="{{ route('manager.subjects.show', $subject) }}"
                                       class="btn btn-sm btn-outline-info"
                                       title="تفاصيل">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('manager.subjects.edit', $subject) }}"
                                       class="btn btn-sm btn-outline-warning"
                                       title="تعديل">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>




                                    <form action="{{ route('manager.subjects.toggle', $subject) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('هل تريد تغيير حالة المادة؟');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-{{ $subject->is_active ? 'secondary' : 'success' }}"
                                                title="{{ $subject->is_active ? 'تعطيل' : 'تفعيل' }}">
                                            <i class="bi bi-toggle-{{ $subject->is_active ? 'off' : 'on' }}"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    لا توجد مواد لعرضها.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($subjects->hasPages())
                <div class="card-footer">
                    {{ $subjects->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
