@extends('layouts.app')
@section('title','إدارة مدراء المعاهد')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة مدراء المعاهد</h1>
            <a href="{{ route('super-admin.managers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> إضافة مدير معهد جديد
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <form method="GET" action="{{ route('super-admin.managers.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input
                            type="text" name="search"
                            class="form-control"
                            placeholder="🔍 بحث بالإيميل أو الرقم..."
                            value="{{ request('search') }}"
                        >
                    </div>
                    <div class="col-md-3">
                        <select name="sort" class="form-select">
                            <option value="">فرز حسب</option>
                            <option value="email"       @selected(request('sort')=='email')>الإيميل</option>
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
                        <a href="{{ route('super-admin.managers.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> إعادة ضبط
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 text-end">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم الكامل</th>
                            <th>الإيميل</th>
                            <th>المعهد المعين له</th>
                            <th>الهاتف</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الحالة</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($managers as $mgr)
                            <tr>
                                <td>{{ $loop->iteration + ($managers->perPage() * ($managers->currentPage()-1)) }}</td>
                                <td>
                                    @if($mgr->admin)
                                        {{ $mgr->admin->first_name }} {{ $mgr->admin->last_name }}
                                    @else
                                        <span class="text-muted">غير متوفر</span>
                                    @endif
                                </td>
                                <td>{{ $mgr->email }}</td>
                                <td>
                                    @if($mgr->institute)
                                        <a href="{{ route('super-admin.institutes.show', $mgr->institute) }}">
                                            {{ $mgr->institute->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">غير معين</span>
                                    @endif
                                </td>
                                <td>{{ $mgr->admin?->phone ?? '-' }}</td>
                                <td>{{ $mgr->created_at?->format('Y-m-d') ?? '-' }}</td>
                                <td>
                                    @if(method_exists($mgr, 'trashed') ? $mgr->trashed() : ($mgr->deleted_at ?? false))
                                        <span class="badge bg-secondary">محذوف</span>
                                    @else
                                        <span class="badge bg-success">نشط</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group" aria-label="أفعال">
                                        <a href="{{ route('super-admin.managers.edit', $mgr) }}"
                                        class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        @if($mgr->institute)
                                            <form action="{{ route('super-admin.managers.unassign-institute', $mgr) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-secondary"
                                                        title="إلغاء تعيين المعهد"
                                                        onclick="return confirm('هل تريد إلغاء تعيين المعهد عن هذا المدير؟');">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('super-admin.managers.destroy', $mgr) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذا المدير؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    لا توجد مدراء معاهد لعرضهم.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                </div>
            </div>

            @if($managers->hasPages())
                <div class="card-footer">
                    {{ $managers->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
