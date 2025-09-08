{{-- resources/views/manager/admins/edit.blade.php --}}
@extends('layouts.app')
@section('title','تعديل مشرف')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 text-gray-800">تعديل مشرف</h2>
            <a href="{{ route('manager.admins.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-lg"></i> إلغاء
            </a>
        </div>

        {{-- الرسائل --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- النموذج --}}
        <div class="card shadow-sm mb-4 p-4">
            <form action="{{ route('manager.admins.update', $admin) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="row g-3">
                @csrf
                @method('PUT')

                {{-- بيانات الحساب --}}
                <div class="col-12"><h5>بيانات حساب المستخدم</h5></div>

                <div class="col-md-6 text-end">
                    <label class="form-label">الاسم الأول<span class="text-danger">*</span></label>
                    <input type="text" name="first_name"
                           class="form-control form-control-sm @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name',$admin->first_name) }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 text-end">
                    <label class="form-label">اسم العائلة<span class="text-danger">*</span></label>
                    <input type="text" name="last_name"
                           class="form-control form-control-sm @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name',$admin->last_name) }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 text-end">
                    <label class="form-label">البريد الإلكتروني<span class="text-danger">*</span></label>
                    <input type="email" name="email"
                           class="form-control form-control-sm @error('email') is-invalid @enderror"
                           value="{{ old('email',$admin->user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 text-end">
                    <label class="form-label">تاريخ الميلاد</label>
                    <input type="date" name="birthdate"
                           class="form-control form-control-sm @error('birthdate') is-invalid @enderror"
                           value="{{ old('birthdate',$admin->birthdate?->format('Y-m-d')) }}">
                    @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- بيانات المشرف --}}
                <div class="col-12 mt-4"><h5>بيانات المشرف</h5></div>

                <div class="col-md-4 text-end">
                    <label class="form-label d-block">الصورة الحالية</label>
                    @if($admin->image)
                        <img src="{{ asset('storage/app/public/'.$admin->image) }}"
                             width="60" height="60" class="rounded-circle" alt="">
                    @else
                        <span class="text-muted">—</span>
                    @endif
                    <div class="mt-2">
                        <input type="file" name="image"
                               class="form-control form-control-sm @error('image') is-invalid @enderror">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-4 text-end">
                    <label class="form-label">الهاتف</label>
                    <input type="text" name="phone"
                           class="form-control form-control-sm @error('phone') is-invalid @enderror"
                           value="{{ old('phone',$admin->phone) }}">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 text-end">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address"
                           class="form-control form-control-sm @error('address') is-invalid @enderror"
                           value="{{ old('address',$admin->address) }}">
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 text-end">
                    <label class="form-label">كلمة المرور (اتركها فارغة إن لم تتغير)</label>
                    <input type="password" name="password"
                           class="form-control form-control-sm @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 text-end">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation"
                           class="form-control form-control-sm">
                </div>

                {{-- زر الحفظ --}}
                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> تحديث المشرف
                    </button>
                    <a href="{{ route('manager.admins.show',$admin) }}" class="btn btn-outline-secondary">إلغاء</a>
                </div>
            </form>
        </div>

        {{-- قائمة المشرفين الحاليين بالمعهد --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">المشرفون المرتبطون بهذا المعهد</h5>
                <small class="text-muted">إجمالي: {{ $admins->count() }}</small>
            </div>

            <div class="card-body p-0">
                @if($admins->isEmpty())
                    <div class="p-4 text-center text-muted">لا يوجد مشرفون مرتبطون بهذا المعهد.</div>
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
                            @foreach($admins as $a)
                                <tr @class(['table-active' => $a->id === $admin->id])>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $a->first_name }} {{ $a->last_name }}</td>
                                <td>{{ $a->user->email ?? '—' }}</td>
                                <td>{{ $a->phone ?? '—' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('manager.admins.show', $a) }}"
                                       class="btn btn-sm btn-outline-info" title="عرض">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('manager.admins.edit', $a) }}"
                                       class="btn btn-sm btn-outline-warning" title="تعديل">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('manager.admins.destroy', $a) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد؟');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="حذف">
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
