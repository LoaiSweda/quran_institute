{{-- resources/views/super-admin/institutes/create.blade.php --}}

@extends('layouts.app')
@section('title', $institute->exists ? 'تعديل معهد' : 'إنشاء معهد جديد')

@section('content')
    <div class="container-fluid">

        {{-- العنوان وزر إلغاء --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">
                {{ $institute->exists ? 'تعديل معهد' : 'إنشاء معهد جديد' }}
            </h1>
            <a href="{{ route('super-admin.institutes.index') }}" class="btn btn-secondary">
                <i class="bi bi-x-lg"></i> إلغاء
            </a>
        </div>

        {{-- النموذج --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    @if($institute->exists)
                        @method('PUT')
                    @endif

                    {{-- اسم المعهد --}}
                    <div class="col-md-6">
                        <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $institute->name) }}"
                            class="form-control form-control-sm @error('name') is-invalid @enderror"
                            placeholder="أدخل اسم المعهد"
                            required
                        >
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- مدير المعهد --}}
                    @php
                        $selected = old('user_id', $institute->user_id ?? session('new_manager_id'));
                    @endphp
                    <div class="col-md-6">
                        <label for="user_id" class="form-label">مدير المعهد <span class="text-danger">*</span></label>
                        <select
                            id="user_id"
                            name="user_id"
                            class="form-select form-select-sm @error('user_id') is-invalid @enderror"
                            required
                        >
                            <option value="">-- اختر المدير --</option>
                            @foreach($managers as $m)
                                <option value="{{ $m->id }}" {{ $selected == $m->id ? 'selected' : '' }}>
                                    {{ $m->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- العنوان --}}
                    <div class="col-md-12">
                        <label for="address" class="form-label">العنوان</label>
                        <input
                            type="text"
                            id="address"
                            name="address"
                            value="{{ old('address', $institute->address) }}"
                            class="form-control form-control-sm @error('address') is-invalid @enderror"
                            placeholder="العنوان التفصيلي (اختياري)"
                        >
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- شعار المعهد --}}
                    <div class="col-md-6">
                        <label for="image" class="form-label">شعار المعهد</label>
                        <input
                            type="file"
                            id="image"
                            name="image"
                            class="form-control form-control-sm @error('image') is-invalid @enderror"
                            accept="image/*"
                        >
                        @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- عرض الشعار الحالي --}}
                    @if($institute->image)
                        <div class="col-md-6 d-flex align-items-center">
                            <div>
                                <div class="text-muted mb-1">الشعار الحالي:</div>
                                <img
                                    src="{{ asset('storage/'.$institute->image) }}"
                                    alt="شعار المعهد"
                                    class="rounded"
                                    style="max-height:60px;"
                                >
                            </div>
                        </div>
                    @endif

                    {{-- زر الحفظ --}}
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save"></i>
                            {{ $institute->exists ? 'تحديث البيانات' : 'حفظ المعهد' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- جدول المعاهد الحالية (لا يتم حذفها) --}}
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">المعاهد المنشأة سابقاً</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 text-end">
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
                                    <a href="{{ route('super-admin.institutes.show', $inst) }}"
                                       class="btn btn-sm btn-outline-info" title="عرض التفاصيل">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('super-admin.institutes.edit', $inst) }}"
                                       class="btn btn-sm btn-outline-warning" title="تعديل">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('super-admin.institutes.destroy', $inst) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المعهد؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    لا توجد معاهد منشأة بعد.
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

@push('styles')
    <style>
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .institute-form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .invalid-feedback { display: block; }
    </style>
@endpush
