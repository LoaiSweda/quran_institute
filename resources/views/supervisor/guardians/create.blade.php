{{-- resources/views/admin/guardians/create.blade.php --}}
@extends('layouts.app')
@section('title','إضافة ولي أمر جديد')

@section('content')
    <div class="container-fluid">

        {{-- Header + Cancel --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 text-gray-800">إضافة ولي أمر جديد</h2>
            <button onclick="history.back()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-lg"></i> إلغاء
            </button>
        </div>

        {{-- Form Card --}}
        <div class="card shadow-sm mb-4 p-4">
            <form action="{{ route('admin.guardians.store', request()->only('institute_id')) }}" method="POST" class="row g-3">
                @csrf

                {{-- الاسم الأول --}}
                <div class="col-md-6">
                    <label class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                    <input type="text" name="firstname" value="{{ old('firstname') }}"
                           class="form-control form-control-sm @error('firstname') is-invalid @enderror"
                           required>
                    @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- اسم العائلة --}}
                <div class="col-md-6">
                    <label class="form-label">اسم العائلة <span class="text-danger">*</span></label>
                    <input type="text" name="lastname" value="{{ old('lastname') }}"
                           class="form-control form-control-sm @error('lastname') is-invalid @enderror"
                           required>
                    @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- الهاتف --}}
                <div class="col-md-6">
                    <label class="form-label">الهاتف <span class="text-danger">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="form-control form-control-sm @error('phone') is-invalid @enderror"
                           required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- العنوان --}}
                <div class="col-md-6">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           class="form-control form-control-sm @error('address') is-invalid @enderror">
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- البريد الإلكتروني --}}
                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control form-control-sm @error('email') is-invalid @enderror"
                           required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- كلمة المرور --}}
                <div class="col-md-6">
                    <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                    <input type="password" name="password"
                           class="form-control form-control-sm @error('password') is-invalid @enderror"
                           required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- تأكيد كلمة المرور --}}
                <div class="col-md-6">
                    <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation"
                           class="form-control form-control-sm" required>
                </div>

                {{-- Submit --}}
                <div class="col-12 text-center mt-3">
                    <button class="btn btn-success">
                        <i class="bi bi-save"></i> حفظ ولي الأمر
                    </button>
                </div>
            </form>
        </div>

        {{-- Existing Guardians List --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">أولياء الأمور المنشأون حديثًا</h5>
                @isset($inst)
                    <small class="text-muted">المعهد: {{ $inst->name }}</small>
                @endisset
            </div>
            <div class="card-body p-0">
                @if($guardians->count())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 text-end">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الهاتف</th>
                                <th>البريد الإلكتروني</th>
                                <th>عدد الطلاب</th>
                                <th>تاريخ الإنشاء</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($guardians as $g)
                                <tr>
                                    <td>{{ $loop->iteration + ($guardians->currentPage()-1)*$guardians->perPage() }}</td>
                                    <td>{{ $g->name }}</td>
                                    <td>{{ $g->phone }}</td>
                                    <td>{{ $g->user->email }}</td>
                                    <td>{{ $g->students->count() }}</td>
                                    <td>{{ $g->created_at->format('Y-m-d') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.guardians.edit', array_merge(['guardian'=>$g->id], request()->only('institute_id'))) }}"
                                           class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.guardians.destroy', array_merge(['guardian'=>$g->id], request()->only('institute_id'))) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('هل تريد حذف هذا ولي الأمر؟');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="حذف">
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
                    <p class="p-4 text-center text-muted">
                        لا يوجد أولياء أمور منشأون بعد.
                    </p>
                @endif
            </div>
            @if($guardians->hasPages())
                <div class="card-footer">
                    {{ $guardians->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

    </div>
@endsection
