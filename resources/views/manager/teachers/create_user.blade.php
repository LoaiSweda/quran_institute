{{-- resources/views/manager/teachers/create.blade.php --}}
@extends('layouts.app')
@section('title','إضافة مدرس جديد')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 text-gray-800">إضافة مدرس جديد</h2>
            {{-- زرّ الإلغاء يذهب دائماً إلى index --}}
            <a href="{{ route('manager.teachers.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-lg"></i> إلغاء
            </a>
        </div>

        {{-- الرسائل --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- النموذج --}}
        <div class="card shadow-sm mb-4 p-4">
            <form action="{{ route('manager.teachers.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="row g-3">
                @csrf

                {{-- بيانات الحساب --}}
                <div class="col-12"><h5>بيانات حساب المستخدم</h5></div>

                <div class="col-md-6 text-end">
                    <label class="form-label">الاسم الكامل<span class="text-danger">*</span></label>
                    <input type="text" name="name"
                           class="form-control form-control-sm @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 text-end">
                    <label class="form-label">البريد الإلكتروني<span class="text-danger">*</span></label>
                    <input type="email" name="email"
                           class="form-control form-control-sm @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 text-end">
                    <label class="form-label">كلمة المرور<span class="text-danger">*</span></label>
                    <input type="password" name="password"
                           class="form-control form-control-sm @error('password') is-invalid @enderror"
                           required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 text-end">
                    <label class="form-label">تأكيد كلمة المرور<span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation"
                           class="form-control form-control-sm @error('password_confirmation') is-invalid @enderror"
                           required>
                    @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- بيانات المدرّس --}}
                <div class="col-12 mt-4"><h5>بيانات المدرّس</h5></div>

                <div class="col-md-4 text-end">
                    <label class="form-label">الصورة</label>
                    <input type="file" name="image"
                           class="form-control form-control-sm @error('image') is-invalid @enderror">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 text-end">
                    <label class="form-label">الاسم الأول<span class="text-danger">*</span></label>
                    <input type="text" name="first_name"
                           class="form-control form-control-sm @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name') }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 text-end">
                    <label class="form-label">اسم العائلة<span class="text-danger">*</span></label>
                    <input type="text" name="last_name"
                           class="form-control form-control-sm @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name') }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 text-end">
                    <label class="form-label">الهاتف</label>
                    <input type="text" name="phone"
                           class="form-control form-control-sm @error('phone') is-invalid @enderror"
                           value="{{ old('phone') }}">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 text-end">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address"
                           class="form-control form-control-sm @error('address') is-invalid @enderror"
                           value="{{ old('address') }}">
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 text-end">
                    <label class="form-label">تاريخ الميلاد</label>
                    <input type="date" name="birthdate"
                           class="form-control form-control-sm @error('birthdate') is-invalid @enderror"
                           value="{{ old('birthdate') }}">
                    @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- زر الحفظ --}}
                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> حفظ المدرّس
                    </button>
                </div>
            </form>
        </div>

        {{-- قائمة المدرّسين الحاليين بالمَعهد --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">المدرّسون المرتبطون بهذا المعهد</h5>
                <small class="text-muted">إجمالي: {{ $teachers->count() }}</small>
            </div>

            <div class="card-body p-0">
                @if($teachers->isEmpty())
                    <div class="p-4 text-center text-muted">لا يوجد مدرسون مرتبطون بهذا المعهد.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 text-end">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>البريد</th>
                                <th>الهاتف</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($teachers as $t)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $t->first_name }} {{ $t->last_name }}
                                    </td>
                                    <td>{{ $t->user->email ?? '—' }}</td>
                                    <td>{{ $t->phone ?? '—' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('manager.teachers.show', $t->id) }}"
                                           class="btn btn-sm btn-outline-info" title="عرض">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('manager.teachers.edit', $t->id) }}"
                                           class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('manager.teachers.destroy', $t->id) }}"
                                              method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="تعطيل">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
