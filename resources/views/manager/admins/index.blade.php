{{-- resources/views/manager/admins/index.blade.php --}}
@extends('layouts.app')
@section('title','إدارة المشرفين')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة المشرفين</h1>
            <a href="{{ route('manager.admins.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> إضافة مشرف جديد
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <form method="GET" action="{{ route('manager.admins.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input
                            type="text" name="search"
                            class="form-control"
                            placeholder="🔍 بحث بالاسم أو البريد..."
                            value="{{ request('search') }}"
                        >
                    </div>
                    <div class="col-md-3">
                        <select name="sort" class="form-select">
                            <option value="">فرز حسب</option>
                            <option value="first_name" @selected(request('sort')=='first_name')>الاسم الأول</option>
                            <option value="last_name"  @selected(request('sort')=='last_name')>اسم العائلة</option>
                            <option value="birthdate"  @selected(request('sort')=='birthdate')>تاريخ الميلاد</option>
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
                        <a href="{{ route('manager.admins.index') }}" class="btn btn-outline-secondary">
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
                            <th>العنوان</th>
                            <th>تاريخ الميلاد</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($admins as $admin)
                            <tr>
                                <td>{{ $loop->iteration + ($admins->perPage() * ($admins->currentPage()-1)) }}</td>
                                <td>
                                    @if($admin->image)
                                        <img src="{{ asset('storage/app/public/'.$admin->image) }}"
                                             alt="صورة المشرف"
                                             width="40" height="40"
                                             class="rounded-circle">
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('manager.admins.show', $admin) }}">
                                        {{ $admin->first_name }} {{ $admin->last_name }}
                                    </a>
                                </td>
                                <td>{{ $admin->user->email ?? '—' }}</td>
                                <td>{{ $admin->phone ?? '—' }}</td>
                                <td>{{ $admin->address ?? '—' }}</td>
                                <td>{{ $admin->birthdate?->format('Y-m-d') ?? '—' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('manager.admins.show', $admin) }}"
                                       class="btn btn-sm btn-outline-info" title="عرض التفاصيل">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('manager.admins.edit', $admin) }}"
                                       class="btn btn-sm btn-outline-warning" title="تعديل">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('manager.admins.destroy', $admin) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المشرف؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    لا يوجد مشرفون لعرضهم.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($admins->hasPages())
                <div class="card-footer">
                    {{ $admins->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
