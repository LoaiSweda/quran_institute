{{-- resources/views/manager/teachers/create_user.blade.php --}}

@extends('layouts.app')
@section('title','إنشاء مستخدم جديد')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">إنشاء مستخدم جديد للمُدرِّس</h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('manager.teachers.storeUser') }}" method="POST">
                    @csrf

                    <div class="mb-3 text-end">
                        <label class="form-label">الاسم</label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3 text-end">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3 text-end">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3 text-end">
                        <label class="form-label">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation"
                               class="form-control" required>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('manager.teachers.create') }}" class="btn btn-secondary">إلغاء</a>
                        <button type="submit" class="btn btn-success">إنشاء المستخدم</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
