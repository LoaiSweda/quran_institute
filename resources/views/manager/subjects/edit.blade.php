{{-- resources/views/manager/subjects/edit.blade.php --}}
@extends('layouts.app')
@section('title','تعديل المادة: '.$subject->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">تعديل المادة</h1>
            <a href="{{ route('manager.subjects.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> رجوع لقائمة المواد
            </a>
        </div>

        <div class="card shadow-sm mb-4 p-4">
            <form action="{{ route('manager.subjects.update', $subject) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label for="name" class="form-label">اسم المادة <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $subject->name) }}"
                           class="form-control form-control-sm @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="level" class="form-label">المستوى</label>
                    <input type="text" id="level" name="level" value="{{ old('level', $subject->level) }}"
                           class="form-control form-control-sm @error('level') is-invalid @enderror">
                    @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea id="description" name="description" rows="3"
                              class="form-control form-control-sm @error('description') is-invalid @enderror">{{ old('description', $subject->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="start_date" class="form-label">تاريخ البداية</label>
                    <input type="date" id="start_date" name="start_date"
                           value="{{ old('start_date', $subject->start_date?->format('Y-m-d')) }}"
                           class="form-control form-control-sm @error('start_date') is-invalid @enderror">
                    @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="end_date" class="form-label">تاريخ النهاية</label>
                    <input type="date" id="end_date" name="end_date"
                           value="{{ old('end_date', $subject->end_date?->format('Y-m-d')) }}"
                           class="form-control form-control-sm @error('end_date') is-invalid @enderror">
                    @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="degree" class="form-label">الدرجة</label>
                    <input type="number" step="0.01" id="degree" name="degree"
                           value="{{ old('degree', $subject->degree) }}"
                           class="form-control form-control-sm @error('degree') is-invalid @enderror">
                    @error('degree') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="total_sessions" class="form-label">إجمالي الحلقات</label>
                    <input type="number" id="total_sessions" name="total_sessions"
                           value="{{ old('total_sessions', $subject->total_sessions) }}"
                           class="form-control form-control-sm @error('total_sessions') is-invalid @enderror">
                    @error('total_sessions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input"
                            {{ old('is_active', $subject->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">نشط</label>
                    </div>
                </div>

                <div class="col-12 text-center mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> تحديث المادة
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
