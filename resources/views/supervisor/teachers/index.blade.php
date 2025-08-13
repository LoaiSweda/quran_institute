@extends('layouts.app')
@section('title','إدارة المدرّسين (عرض المشرف)')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة المدرّسين</h1>
            {{-- المشرف: عرض فقط، لا زر إضافة --}}
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <form method="GET" action="{{ route('admin.teachers.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input
                            type="text" name="search"
                            class="form-control"
                            placeholder="🔍 بحث بالاسم/البريد/الهاتف..."
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-3">
                        <select name="institute_id" class="form-select">
                            <option value="">كل المعاهد</option>
                            @foreach($institutes as $inst)
                                <option value="{{ $inst->id }}" @selected(request('institute_id')==$inst->id)>
                                {{ $inst->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="class_id" class="form-select">
                            <option value="">كل الحلقات</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}" @selected(request('class_id')==$cls->id)>
                                {{ $cls->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1">
                        <select name="sort" class="form-select">
                            <option value="">فرز</option>
                            <option value="name" @selected(request('sort')=='name')>الاسم</option>
                            <option value="classes_count" @selected(request('sort')=='classes_count')>عدد الحلقات</option>
                            <option value="created_at" @selected(request('sort')=='created_at')>تاريخ الإضافة</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <select name="direction" class="form-select">
                            <option value="asc"  @selected(request('direction')=='asc')>تصاعدي</option>
                            <option value="desc" @selected(request('direction')=='desc')>تنازلي</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-12 text-md-end mt-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-funnel-fill"></i> تطبيق
                        </button>
                        <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> إعادة ضبط
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive text-end">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>البريد</th>
                            <th>الهاتف</th>
                            <th>المعاهد</th>
                            <th>عدد الحلقات</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($teachers as $t)
                            <tr>
                                <td>{{ $loop->iteration + ($teachers->perPage() * ($teachers->currentPage()-1)) }}</td>

                                <td>
                                    @if($t->image)
                                        <img src="{{ asset('storage/'.$t->image) }}"
                                             alt="صورة المدرس" width="40" height="40"
                                             class="rounded-circle" style="object-fit:cover">
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('admin.teachers.show', $t) }}">
                                        {{ $t->first_name }} {{ $t->last_name }}
                                    </a>
                                </td>

                                <td>{{ $t->user->email ?? '—' }}</td>
                                <td>{{ $t->phone ?? '—' }}</td>

                                <td>
                                    @if($t->institutes && $t->institutes->count())
                                        {{ $t->institutes->pluck('name')->join('، ') }}
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>{{ $t->teaching_classes_count ?? $t->teachingClasses->count() }}</td>

                                <td class="text-center">
                                    <a href="{{ route('admin.teachers.show', $t) }}"
                                       class="btn btn-sm btn-outline-info" title="عرض التفاصيل">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    {{-- عرض فقط للمشرف، لا تعديل/حذف --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    لا يوجد مدرسون لعرضهم.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($teachers->hasPages())
                <div class="card-footer">
                    {{ $teachers->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
