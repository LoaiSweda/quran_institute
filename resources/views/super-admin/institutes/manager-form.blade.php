@extends('layouts.app')
@section('title','إنشاء مدير معهد جديد')

@section('content')
    <div class="container">
        <h1 class="h3">إنشاء مدير معهد جديد</h1>
        <form action="{{ route('super-admin.institutes.manager.store') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label class="form-label">الإسم (اختياري)</label>
                <input type="text"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}">
                @error('name') <div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                <input type="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                <input type="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required>
                @error('password') <div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                <input type="password"
                       name="password_confirmation"
                       class="form-control" required>
            </div>
            <div class="col-12">
                <button class="btn btn-success">
                    <i class="bi bi-person-plus"></i> حفظ المدير
                </button>
                <a href="{{ route('super-admin.institutes.create') }}" class="btn btn-secondary">
                    تراجع
                </a>
            </div>
        </form>
    </div>
@endsection
