@extends('layouts.app')

@section('title', $institute->exists ? 'تعديل معهد' : 'إنشاء معهد جديد')

@section('content')
    <div class="container-fluid">
        <!-- العنوان والـ Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">{{ $institute->exists ? 'تعديل معهد' : 'إنشاء معهد جديد' }}</h1>
            <a href="{{ route('super-admin.institutes.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> الرجوع للقائمة
            </a>
        </div>

        <!-- البطاقة الرئيسية -->
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    @if($institute->exists)
                        @method('PUT')
                    @endif

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

                    <div class="col-md-6">
                        <label for="user_id" class="form-label">مدير المعهد <span class="text-danger">*</span></label>
                        <select
                            id="user_id"
                            name="user_id"
                            class="form-select form-select-sm @error('user_id') is-invalid @enderror"
                            required
                        >
                            <option value="">اختر المدير</option>
                            @foreach($managers as $u)
                                <option value="{{ $u->id }}"
                                    {{ old('user_id', $institute->user_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

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

                <!-- زر الحفظ مركزي -->
                    <div class="col-12 text-center mt-3">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save"></i>
                            {{ $institute->exists ? 'تحديث البيانات' : 'حفظ المعهد' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
