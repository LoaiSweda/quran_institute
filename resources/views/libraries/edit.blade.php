<!-- resources/views/libraries/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <h1 class="display-6 fw-bold mb-3 mb-md-0">
                <i class="fas fa-edit text-primary me-2"></i> تعديل مورد المكتبة: {{ $library->name }}
            </h1>
            <a href="{{ route(
                Auth::user()->hasRole('super admin') ? 'super-admin.library.index' :
                (Auth::user()->hasRole('institute manager') ? 'manager.library.index' :
                (Auth::user()->hasRole('admin') ? 'admin.library.index' : 'teacher.library.index'))
            ) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> العودة إلى المكتبة
            </a>

        </div>

        {{-- Error messages display --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div>
                        <strong>خطأ!</strong> يرجى تصحيح الأخطاء التالية:
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

        {{-- Edit Form --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route(
                    Auth::user()->hasRole('super admin') ? 'super-admin.library.update' :
                    (Auth::user()->hasRole('institute manager') ? 'manager.library.update' :
                    (Auth::user()->hasRole('admin') ? 'admin.library.update' : 'teacher.library.update')),
                    $library->id
                ) }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        {{-- Resource Name --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">اسم المورد:</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $library->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-semibold">التصنيف:</label>
                            <select name="category_id" id="category_id"
                                    class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">اختر تصنيفاً</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $library->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
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
                                   class="form-control @error('author') is-invalid @enderror"
                                   value="{{ old('author', $library->author) }}">
                            @error('author')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ISBN --}}
                        <div class="col-md-6">
                            <label for="isbn" class="form-label fw-semibold">رقم ISBN (اختياري):</label>
                            <input type="text" name="isbn" id="isbn"
                                   class="form-control @error('isbn') is-invalid @enderror"
                                   value="{{ old('isbn', $library->isbn) }}">
                            @error('isbn')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">الوصف:</label>
                        <textarea name="description" id="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $library->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- File Upload --}}
                    <div class="mb-4">
                        <label for="file" class="form-label fw-semibold">
                            <i class="fas fa-file-upload text-primary me-2"></i> تغيير الملف:
                            <span class="text-muted small">(اترك فارغاً للإبقاء على الملف الحالي)</span>
                        </label>
                        <input type="file" name="file" id="file"
                               class="form-control @error('file') is-invalid @enderror">
                        @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($library->file)
                            <div class="mt-2">
                                <span class="text-muted small">الملف الحالي:</span>
                                <a href="{{ $library->file->url }}" target="_blank" class="d-block text-primary">
                                    <i class="fas fa-file me-2"></i> {{ $library->file->original_name }}
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Visibility checkbox for Super Admin and Institute Manager --}}
                    @if(Auth::user()->hasAnyRole(['super admin','institute manager','admin']))
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   name="is_visible" id="is_visible"
                                {{ old('is_visible', $library->is_visible) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_visible">
                                مرئي للطلاب
                            </label>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-save me-2"></i> تحديث المورد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js" integrity="sha512-fzff82+8pzHnwA1mQ0dzz9/E0B+ZRizq08yZfya66INZBz86qKTCt9MLU0NCNIgaMJCgeyhujhasnFUsYMsi0Q==" crossorigin="anonymous"></script>
@endpush
