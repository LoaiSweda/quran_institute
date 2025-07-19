{{-- resources/views/manager/classes/create.blade.php --}}
@extends('layouts.app')
@section('title','إنشاء حلقة جديدة')

@section('content')
    <div class="container-fluid">

        {{-- Header + Back Button --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">إنشاء حلقة جديدة</h1>
            <a href="{{ route('manager.classes.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> رجوع للقائمة
            </a>
        </div>

        {{-- Form Card --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">بيانات الحلقة</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('manager.classes.store') }}" method="POST">
                    @csrf

                    {{-- اسم الحلقة --}}
                    <div class="mb-3">
                        <label class="form-label">اسم الحلقة</label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- المادة --}}
                    <div class="mb-3">
                        <label class="form-label">المادة</label>
                        <select name="subject_id"
                                class="form-select @error('subject_id') is-invalid @enderror"
                                required>
                            <option value="">اختر المادة</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}"
                                        @selected(old('subject_id') == $sub->id)>
                                {{ $sub->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- المدرّس --}}
                    <div class="mb-3">
                        <label class="form-label">اسم المدرّس</label>
                        <select name="user_id" id="user_id"
                                class="form-select @error('user_id') is-invalid @enderror"
                                required>
                            <option value="">اختر المدرّس</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}"
                                        @selected(old('user_id') == $t->id)>
                                {{ $t->first_name }} {{ $t->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- عدد الطلاب --}}
                    <div class="mb-3">
                        <label class="form-label">عدد الطلاب</label>
                        <input type="number" name="students_count"
                               class="form-control @error('students_count') is-invalid @enderror"
                               value="{{ old('students_count',1) }}"
                               min="1" required>
                        @error('students_count')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="text-start">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> إنشاء الحلقة
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Existing Classes --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">الحلقات المنشأة سابقاً</h6>
            </div>
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
                                <td class="text-center">
                                    <a href="{{ route('manager.classes.show',$cls) }}"
                                       class="btn btn-sm btn-outline-info" title="عرض">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('manager.classes.edit',$cls) }}"
                                       class="btn btn-sm btn-outline-warning" title="تعديل">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('manager.classes.destroy',$cls) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('هل تريد حذف الحلقة؟');">
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
                                    لا توجد حلقات
                                </td>
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
