@extends('layouts.app')
@section('title', $user->exists ? 'تعديل مدير معهد' : 'إنشاء مدير معهد جديد')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">
                {{ $user->exists ? 'تعديل مدير معهد' : 'إنشاء مدير معهد جديد' }}
            </h1>
            <a href="{{ route('super-admin.managers.index') }}" class="btn btn-secondary">
                <i class="bi bi-x-lg"></i> إلغاء
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ $user->exists ? route('super-admin.managers.update', $user) : route('super-admin.managers.store') }}"
                    method="POST"
                    enctype="multipart/form-data" {{-- مهم لرفع الصورة --}}
                    class="row g-3">
                    @csrf
                    @if($user->exists)
                        @method('PUT')
                    @endif

                    {{-- الإيميل --}}
                    <div class="col-md-6">
                        <label for="email" class="form-label">الإيميل <span class="text-danger">*</span></label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="form-control form-control-sm @error('email') is-invalid @enderror"
                            placeholder="example@mail.com"
                            required
                        >
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- كلمة المرور --}}
                    <div class="col-md-6">
                        <label for="password" class="form-label">
                            {{ $user->exists ? 'كلمة المرور (اتركه فارغاً إذا لم تتغير)' : 'كلمة المرور' }}
                            <span class="text-danger">{{ $user->exists ? '' : '*' }}</span>
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control form-control-sm @error('password') is-invalid @enderror"
                            {{ $user->exists ? '' : 'required' }}
                            placeholder="••••••••"
                        >
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- تأكيد كلمة المرور --}}
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">
                            تأكيد كلمة المرور
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control form-control-sm"
                            placeholder="••••••••"
                        >
                    </div>

                    {{-- الاسم الأول --}}
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="{{ old('first_name', $user->admin?->first_name) }}"
                            class="form-control form-control-sm @error('first_name') is-invalid @enderror"
                            placeholder="الاسم الأول"
                            required
                        >
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- اسم العائلة --}}
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">اسم العائلة <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="{{ old('last_name', $user->admin?->last_name) }}"
                            class="form-control form-control-sm @error('last_name') is-invalid @enderror"
                            placeholder="اسم العائلة"
                            required
                        >
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- الهاتف --}}
                    <div class="col-md-6">
                        <label for="phone" class="form-label">الهاتف</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $user->admin?->phone) }}"
                            class="form-control form-control-sm @error('phone') is-invalid @enderror"
                            placeholder="رقم الهاتف"
                        >
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- العنوان --}}
                    <div class="col-md-6">
                        <label for="address" class="form-label">العنوان</label>
                        <input
                            type="text"
                            id="address"
                            name="address"
                            value="{{ old('address', $user->admin?->address) }}"
                            class="form-control form-control-sm @error('address') is-invalid @enderror"
                            placeholder="العنوان"
                        >
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- تاريخ الميلاد --}}
                    <div class="col-md-6">
                        <label for="birthdate" class="form-label">تاريخ الميلاد</label>
                        <input
                            type="date"
                            id="birthdate"
                            name="birthdate"
                            value="{{ old('birthdate', optional($user->admin)->birthdate?->format('Y-m-d')) }}"
                            class="form-control form-control-sm @error('birthdate') is-invalid @enderror"
                        >
                        @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- صورة الملف الشخصي --}}
                    <div class="col-md-6">
                        <label for="image" class="form-label">صورة المدير</label>
                        <input
                            type="file"
                            id="image"
                            name="image"
                            class="form-control form-control-sm @error('image') is-invalid @enderror"
                            accept="image/*"
                        >
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- عرض الصورة الحالية إذا كانت موجودة --}}
                    @if($user->admin?->image)
                        <div class="col-md-6 d-flex align-items-center">
                            <div>
                                <div class="text-muted mb-1">الصورة الحالية:</div>
                                <img
                                    src="{{ asset('storage/'.$user->admin->image) }}"
                                    alt="صورة المدير"
                                    class="rounded"
                                    style="max-height:60px;"
                                >
                            </div>
                        </div>
                    @endif

                    {{-- تعيين معهد --}}
                    <div class="col-md-6">
                        <label for="institute_id" class="form-label">تعيين معهد</label>
                        <select name="institute_id" id="institute_id"
                                class="form-select form-select-sm @error('institute_id') is-invalid @enderror">
                            <option value="">-- غير معين --</option>
                            @foreach($institutes as $inst)
                                <option value="{{ $inst->id }}"
                                    @if(old('institute_id', $user->institute?->id) == $inst->id) selected @endif>
                                    {{ $inst->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('institute_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- زر الحفظ --}}
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save"></i>
                            {{ $user->exists ? 'تحديث المدير' : 'حفظ المدير' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
@endsection
