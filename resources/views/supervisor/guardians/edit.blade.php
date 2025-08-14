{{-- resources/views/admin/guardians/edit.blade.php --}}
@extends('layouts.app')
@section('title','تعديل ولي أمر')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 text-gray-800">تعديل بيانات ولي الأمر</h2>
            <a href="{{ route('admin.guardians.index', request()->only('institute_id')) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-lg"></i> إلغاء
            </a>
        </div>

        <div class="card shadow-sm mb-4 p-4">
            <form action="{{ route('admin.guardians.update', array_merge(['guardian'=>$guardian->id], request()->only('institute_id'))) }}"
                  method="POST" class="row g-3">
                @csrf @method('PUT')

                {{-- الاسم الأول --}}
                <div class="col-md-6">
                    <label class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                    <input type="text" name="firstname"
                           value="{{ old('firstname', $guardian->firstname ?? $guardian->first_name) }}"
                           class="form-control form-control-sm @error('firstname') is-invalid @enderror"
                           required>
                    @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- اسم العائلة --}}
                <div class="col-md-6">
                    <label class="form-label">اسم العائلة <span class="text-danger">*</span></label>
                    <input type="text" name="lastname"
                           value="{{ old('lastname', $guardian->lastname ?? $guardian->last_name) }}"
                           class="form-control form-control-sm @error('lastname') is-invalid @enderror"
                           required>
                    @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- الهاتف --}}
                <div class="col-md-6">
                    <label class="form-label">الهاتف <span class="text-danger">*</span></label>
                    <input type="text" name="phone"
                           value="{{ old('phone', $guardian->phone) }}"
                           class="form-control form-control-sm @error('phone') is-invalid @enderror"
                           required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- العنوان --}}
                <div class="col-md-6">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address"
                           value="{{ old('address', $guardian->address) }}"
                           class="form-control form-control-sm @error('address') is-invalid @enderror">
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- البريد الإلكتروني --}}
                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" name="email"
                           value="{{ old('email', $guardian->user->email) }}"
                           class="form-control form-control-sm @error('email') is-invalid @enderror"
                           required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- كلمة المرور الجديدة --}}
                <div class="col-md-6">
                    <label class="form-label">كلمة المرور الجديدة <small class="text-muted">(اختياري)</small></label>
                    <input type="password" name="password"
                           class="form-control form-control-sm @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- تأكيد كلمة المرور --}}
                <div class="col-md-6">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation"
                           class="form-control form-control-sm">
                </div>

                {{-- Submit --}}
                <div class="col-12 text-center mt-3">
                    <button class="btn btn-warning">
                        <i class="bi bi-save"></i> تحديث ولي الأمر
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
