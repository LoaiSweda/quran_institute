{{-- resources/views/manager/teachers/create.blade.php --}}
@extends('layouts.app')
@section('title','إضافة مدرس جديد')

@section('content')
    <div class="container-fluid">
        {{-- Header + Cancel Button --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 text-gray-800">إضافة مدرس جديد</h2>
            <button onclick="history.back()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-lg"></i> إلغاء
            </button>
        </div>

        {{-- Form Card --}}
        <div class="card shadow-sm mb-4 p-4">
            <form action="{{ route('manager.teachers.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf

                {{-- ========== بيانات حساب المستخدم ========== --}}
                <div class="col-12"><h5 class="mb-3">بيانات حساب المستخدم</h5></div>

                <div class="col-md-6">
                    <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name"
                           class="form-control form-control-sm @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email"
                           class="form-control form-control-sm @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                    <input type="password" id="password" name="password"
                           class="form-control form-control-sm @error('password') is-invalid @enderror" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control form-control-sm @error('password_confirmation') is-invalid @enderror" required>
                    @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- ========== بيانات المدرس ========== --}}
                <div class="col-12 mt-4"><h5 class="mb-3">بيانات المدرس</h5></div>

                <div class="col-md-6">
                    <label for="image" class="form-label">الصورة</label>
                    <input type="file" id="image" name="image"
                           class="form-control form-control-sm @error('image') is-invalid @enderror">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="first_name" class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                    <input type="text" id="first_name" name="first_name"
                           class="form-control form-control-sm @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name') }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="last_name" class="form-label">اسم العائلة <span class="text-danger">*</span></label>
                    <input type="text" id="last_name" name="last_name"
                           class="form-control form-control-sm @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name') }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label">الهاتف</label>
                    <input type="text" id="phone" name="phone"
                           class="form-control form-control-sm @error('phone') is-invalid @enderror"
                           value="{{ old('phone') }}">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="address" class="form-label">العنوان</label>
                    <input type="text" id="address" name="address"
                           class="form-control form-control-sm @error('address') is-invalid @enderror"
                           value="{{ old('address') }}">
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="birthdate" class="form-label">تاريخ الميلاد</label>
                    <input type="date" id="birthdate" name="birthdate"
                           class="form-control form-control-sm @error('birthdate') is-invalid @enderror"
                           value="{{ old('birthdate') }}">
                    @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Submit --}}
                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> حفظ
                    </button>
                </div>
            </form>
        </div>

        {{-- Existing Teachers List --}}
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">المعلمون المنشأون  ({{ $teachers->count() }})</h5>
            </div>
            <div class="card-body p-0">
                @if($teachers->count())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 text-end">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم الكامل</th>
                                <th>البريد الإلكتروني</th>
                                <th>الهاتف</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($teachers as $t)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $t->first_name }} {{ $t->last_name }}</td>
                                    <td>{{ $t->email }}</td>
                                    <td>{{ $t->phone }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('manager.teachers.edit', $t->id) }}"
                                           class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('manager.teachers.destroy', $t->id) }}"
                                              method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المدرس؟');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="p-4 text-center text-muted">لا يوجد معلمون منشأون بعد.</p>
                @endif
            </div>
            {{-- إذا كنت تستخدم الباجينيشن: --}}
            {{-- <div class="card-footer">
                {{ $teachers->links('pagination::bootstrap-5') }}
            </div> --}}
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .container-fluid h2 {
            font-size: 1.5rem;
        }
        .card.p-4 {
            padding: 1.5rem !important;
        }
    </style>
@endpush
