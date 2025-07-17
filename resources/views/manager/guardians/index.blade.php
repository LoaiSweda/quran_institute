{{-- resources/views/manager/guardians/index.blade.php --}}
@extends('layouts.app')
@section('title','إدارة أولياء الأمور')

@section('content')
    <div class="container-fluid">

        {{-- Header + Add Button --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">إدارة أولياء الأمور</h1>
            <a href="{{ route('manager.guardians.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> إضافة ولي أمر جديد
            </a>
        </div>

        {{-- Card للفلاتر --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <form method="GET" action="{{ route('manager.guardians.index') }}" class="row g-3 align-items-end">
                    {{-- بحث بالاسم --}}
                    <div class="col-md-4">
                        <label class="form-label">الاسم أو الهاتف</label>
                        <input type="text" name="search"
                               value="{{ request('search') }}"
                               placeholder="ابحث بالاسم أو الهاتف"
                               class="form-control form-control-sm">
                    </div>
                    {{-- زر تطبيق / إعادة ضبط --}}
                    <div class="col-md-2 text-end">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-funnel-fill"></i> تطبيق
                        </button>
                        <a href="{{ route('manager.guardians.index') }}"
                           class="btn btn-outline-secondary btn-sm w-100 mt-1">
                            إعادة ضبط
                        </a>
                    </div>
                </form>
            </div>

            {{-- جدول النتائج --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 text-end">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الهاتف</th>
                            <th>العنوان</th>
                            <th>عدد الطلاب</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($guardians as $guardian)
                            <tr>
                                <td>{{ $loop->iteration + ($guardians->currentPage()-1)*$guardians->perPage() }}</td>
                                <td>{{ $guardian->name }}</td>
                                <td>{{ $guardian->phone }}</td>
                                <td>{{ $guardian->address ?? '—' }}</td>
                                <td>{{ $guardian->students->count() }}</td>
                                <td class="text-center">
                                    <a href="{{ route('manager.guardians.show',$guardian) }}"
                                       class="btn btn-sm btn-outline-info" title="عرض">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('manager.guardians.edit',$guardian) }}"
                                       class="btn btn-sm btn-outline-warning" title="تعديل">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('manager.guardians.destroy',$guardian) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('هل تريد حذف/تعطيل هذا ولي الأمر؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    لا يوجد أولياء أمور
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($guardians->hasPages())
                <div class="card-footer">
                    {{ $guardians->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
