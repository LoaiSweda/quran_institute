{{-- resources/views/manager/teachers/create.blade.php --}}
@extends('layouts.app')
@section('title','إضافة مدرس')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">إضافة مدرس جديد</h1>
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('manager.teachers.store') }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- اختر المدرّس أو أضف جديد --}}
                    <div class="mb-3 text-end d-flex align-items-center">
                        <div class="flex-grow-1">
                            <label class="form-label">اختر المدرّس (User)</label>
                            <select name="user_id"
                                    class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">-- اختر حساب المدرّس --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}"
                                        {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- زر إضافة مدرس جديد بجانب الاختيار --}}
                        <div class="ms-3" style="margin-top: 32px;">
                            <a href="{{ route('manager.teachers.newUser') }}"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-plus-lg"></i> مدرس جديد
                            </a>

                        </div>
                    </div>

                    {{-- باقي الحقول … --}}
                    <div class="row gy-3">
                        {{-- الصورة --}}
                        <div class="col-md-6 text-end">
                            <label class="form-label">الصورة</label>
                            <input type="file" name="image"
                                   class="form-control @error('image') is-invalid @enderror">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        {{-- الاسم الأول --}}
                        <div class="col-md-6 text-end">
                            <label class="form-label">الاسم الأول</label>
                            <input type="text" name="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name') }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        {{-- اسم العائلة --}}
                        <div class="col-md-6 text-end">
                            <label class="form-label">اسم العائلة</label>
                            <input type="text" name="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name') }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        {{-- الهاتف --}}
                        <div class="col-md-6 text-end">
                            <label class="form-label">الهاتف</label>
                            <input type="text" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        {{-- العنوان --}}
                        <div class="col-md-6 text-end">
                            <label class="form-label">العنوان</label>
                            <input type="text" name="address"
                                   class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address') }}">
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        {{-- تاريخ الميلاد --}}
                        <div class="col-md-6 text-end">
                            <label class="form-label">تاريخ الميلاد</label>
                            <input type="date" name="birthdate"
                                   class="form-control @error('birthdate') is-invalid @enderror"
                                   value="{{ old('birthdate') }}">
                            @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- الأزرار السفلى --}}
                    <div class="mt-4 text-end">
                        <a href="{{ route('manager.teachers.index') }}" class="btn btn-secondary">إلغاء</a>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
