{{-- resources/views/manager/classes/edit.blade.php --}}
@extends('layouts.app')
@section('title',"تعديل الحلقة — {$class->name}")

@section('content')
    <div class="container-fluid">

        {{-- Header + Back --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">تعديل الحلقة</h1>
            <a href="{{ route('manager.classes.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> رجوع
            </a>
        </div>

        {{-- Form Card --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">بيانات الحلقة</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('manager.classes.update', $class) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- اسم الحلقة --}}
                    <div class="mb-3">
                        <label class="form-label">اسم الحلقة</label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $class->name) }}" required>
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
                                        @selected(old('subject_id', $class->subject_id) == $sub->id)>
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
                        <select name="user_id"
                                class="form-select @error('user_id') is-invalid @enderror"
                                required>
                            <option value="">اختر المدرّس</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}"
                                        @selected(old('user_id', $class->user_id) == $t->id)>
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
                               value="{{ old('students_count', $class->students_count) }}"
                               min="1" required>
                        @error('students_count')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="text-start">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
