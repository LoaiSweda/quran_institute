<!-- resources/views/libraries/create.blade.php -->
@extends('layouts.app') {{-- Assuming you have a base layout --}}

@section('title', 'إضافة مورد جديد للمكتبة')

@section('content')
    <div class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <h1 class="display-5 fw-bold text-dark mb-0">
                <i class="fas fa-plus-circle text-primary me-2"></i> إضافة مورد جديد للمكتبة
            </h1>
            <a href="{{ route(Auth::user()->hasRole('super admin') ? 'super-admin.library.index' : (Auth::user()->hasRole('institute manager') ? 'manager.library.index' : 'teacher.library.index')) }}"
               class="btn btn-secondary btn-lg rounded-pill">
                <i class="fas fa-arrow-right-from-bracket me-2"></i> العودة إلى المكتبة
            </a>
        </div>

        {{-- Error messages display --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                    <div>
                        <strong class="fw-bold">خطأ في الإدخال!</strong>
                        <span class="d-block">يرجى مراجعة الحقول التالية:</span>
                    </div>
                </div>
                <ul class="mt-2 mb-0 ps-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Create Form --}}
        <div class="card shadow border-0 mb-5">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route(Auth::user()->hasRole('super admin') ? 'super-admin.library.store' : (Auth::user()->hasRole('institute manager') ? 'manager.library.store' : 'teacher.library.store')) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4 mb-4">
                        {{-- Resource Name --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">اسم المورد:</label>
                            <input type="text" name="name" id="name"
                                   class="form-control form-control-lg @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required
                                   placeholder="مثال: كتاب التجويد الميسر">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-semibold">التصنيف:</label>
                            <select name="category_id" id="category_id"
                                    class="form-select form-select-lg @error('category_id') is-invalid @enderror">
                                <option value="">-- اختر تصنيفاً --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Author --}}
                        <div class="col-md-6">
                            <label for="author" class="form-label fw-semibold">المؤلف (اختياري):</label>
                            <input type="text" name="author" id="author"
                                   class="form-control form-control-lg @error('author') is-invalid @enderror"
                                   value="{{ old('author') }}"
                                   placeholder="مثال: الشيخ فلان بن فلان">
                            @error('author')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ISBN --}}
                        <div class="col-md-6">
                            <label for="isbn" class="form-label fw-semibold">رقم ISBN (اختياري):</label>
                            <input type="text" name="isbn" id="isbn"
                                   class="form-control form-control-lg @error('isbn') is-invalid @enderror"
                                   value="{{ old('isbn') }}"
                                   placeholder="مثال: 978-3-16-148410-0">
                            @error('isbn')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">الوصف:</label>
                        <textarea name="description" id="description" rows="5"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="وصف موجز للمورد ومحتواه...">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- File Upload --}}
                    <div class="mb-4">
                        <label for="file" class="form-label fw-semibold">
                            <i class="fas fa-file-upload text-primary me-2"></i> الملف (PDF, MP3, MP4, DOC, PPT, XLS):
                            <span class="text-muted small">(الحد الأقصى 100MB)</span>
                        </label>
                        <input type="file" name="file" id="file"
                               class="form-control form-control-lg @error('file') is-invalid @enderror"
                               required>
                        @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Visibility checkbox for Super Admin and Institute Manager --}}
                    @if(Auth::user()->hasAnyRole(['super admin', 'institute manager']))
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_visible" id="is_visible" {{ old('is_visible') ? 'checked' : '' }}>
                            <label class="form-check-label fw-medium" for="is_visible">مرئي للطلاب</label>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end mt-5">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 py-3">
                            <i class="fas fa-cloud-upload-alt me-2"></i> إضافة المورد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Font Awesome for icons --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js" integrity="sha512-..." crossorigin="anonymous"></script>
@endpush
