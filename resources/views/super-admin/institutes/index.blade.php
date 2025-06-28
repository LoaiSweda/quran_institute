@extends('layouts.app')
@section('title','إدارة المعاهد')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة المعاهد</h1>
            <a href="{{ route('super-admin.institutes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> إضافة معهد جديد
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <form method="GET" action="{{ route('super-admin.institutes.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input
                            type="text" name="search"
                            class="form-control"
                            placeholder="🔍 بحث بالاسم..."
                            value="{{ request('search') }}"
                        >
                    </div>
                    <div class="col-md-3">
                        <select name="sort" class="form-select">
                            <option value="">فرز حسب</option>
                            <option value="name"       @selected(request('sort')=='name')>الاسم</option>
                            <option value="created_at" @selected(request('sort')=='created_at')>تاريخ الإنشاء</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="direction" class="form-select">
                            <option value="asc"  @selected(request('direction')=='asc')>تصاعدي</option>
                            <option value="desc" @selected(request('direction')=='desc')>تنازلي</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-funnel-fill"></i> تطبيق
                        </button>
                        <a href="{{ route('super-admin.institutes.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> إعادة ضبط
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>المدير</th>
                            <th>الحالة</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($institutes as $inst)
                            <tr>
                                <td>{{ $loop->iteration + ($institutes->perPage() * ($institutes->currentPage()-1)) }}</td>
                                <td>
                                    <a href="{{ route('super-admin.institutes.show', $inst) }}">
                                        {{ $inst->name }}
                                    </a>
                                </td>
                                <td>{{ $inst->manager->email }}</td>
                                <td>
                                    @if($inst->deleted_at)
                                        <span class="badge bg-secondary">معطل</span>
                                    @else
                                        <span class="badge bg-success">نشط</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('super-admin.institutes.edit', $inst) }}"
                                       class="btn btn-sm btn-outline-warning" title="تعديل">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('super-admin.institutes.destroy', $inst) }}"
                                          method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا المعهد؟');"
                                                title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    لا توجد معاهد لعرضها.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($institutes->hasPages())
                <div class="card-footer">
                    {{ $institutes->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
