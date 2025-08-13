{{-- resources/views/admin/students/create.blade.php --}}
@extends('layouts.app')
@section('title','إضافة طالب جديد')

@section('content')
    <div class="container-fluid">
        {{-- Header + Cancel --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 text-gray-800">إضافة طالب جديد</h2>
            <button onclick="history.back()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-lg"></i> إلغاء
            </button>
        </div>

        {{-- Form Card --}}
        <div class="card shadow-sm mb-4 p-4">
            <form action="{{ route('admin.students.store') }}" method="POST" class="row g-3">
                @csrf

                {{-- تمرير المعهد الحالي عند الحاجة --}}
                @isset($inst)
                    <input type="hidden" name="institute_id" value="{{ $inst->id }}">
                @endisset

                {{-- البريد الإلكتروني --}}
                <div class="col-md-4">
                    <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control form-control-sm @error('email') is-invalid @enderror"
                           required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- كلمة المرور --}}
                <div class="col-md-4">
                    <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                    <input type="password" name="password"
                           class="form-control form-control-sm @error('password') is-invalid @enderror"
                           required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- تأكيد كلمة المرور --}}
                <div class="col-md-4">
                    <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation"
                           class="form-control form-control-sm" required>
                </div>

                {{-- الاسم الأول --}}
                <div class="col-md-6">
                    <label class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                           class="form-control form-control-sm @error('first_name') is-invalid @enderror"
                           required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- اسم العائلة --}}
                <div class="col-md-6">
                    <label class="form-label">اسم العائلة <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                           class="form-control form-control-sm @error('last_name') is-invalid @enderror"
                           required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- تاريخ الميلاد --}}
                <div class="col-md-4">
                    <label class="form-label">تاريخ الميلاد</label>
                    <input type="date" name="birthdate" value="{{ old('birthdate') }}"
                           class="form-control form-control-sm">
                </div>

                {{-- الهاتف --}}
                <div class="col-md-4">
                    <label class="form-label">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="form-control form-control-sm">
                </div>

                {{-- العنوان --}}
                <div class="col-md-4">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           class="form-control form-control-sm">
                </div>

                {{-- اسم الأب --}}
                <div class="col-md-6">
                    <label class="form-label">اسم الأب</label>
                    <input type="text" name="father_name" value="{{ old('father_name') }}"
                           class="form-control form-control-sm">
                </div>

                {{-- الوصي --}}
                <div class="col-md-6">
                    <label class="form-label">الوصي</label>
                    <select name="guardian_id" class="form-select form-select-sm bg-white text-dark">
                        <option value="" {{ old('guardian_id') ? '' : 'selected' }}>
                            -- بدون وصي --
                        </option>
                        @foreach($guardians as $g)
                            <option value="{{ $g->id }}" {{ old('guardian_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->name }} {{-- accessor --}}
                                @if($g->user?->email) — {{ $g->user->email }} @endif
                            </option>
                        @endforeach
                    </select>
                    @error('guardian_id')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>


                {{-- Submit --}}
                <div class="col-12 text-center mt-3">
                    <button class="btn btn-success">
                        <i class="bi bi-save"></i> حفظ الطالب
                    </button>
                </div>
            </form>
        </div>

        {{-- Existing Students List --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">الطلاب المنشأون حديثًا</h5>
                @isset($inst)
                    <small class="text-muted">المعهد: {{ $inst->name }}</small>
                @endisset
            </div>
            <div class="card-body p-0">
                @if($students->count())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 text-end">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>البريد</th>
                                <th>كود QR</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>{{ $loop->iteration + ($students->currentPage()-1)*$students->perPage() }}</td>
                                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                    <td>{{ $student->user->email }}</td>
                                    <td>{{ $student->qr }}</td>
                                    <td>
                                        <span class="badge bg-{{ $student->present_percentage >= 75 ? 'success' : ($student->present_percentage >= 50 ? 'warning' : 'secondary') }}">
                                            حضور {{ $student->present_percentage }}%
                                        </span>
                                    </td>
                                    <td>{{ $student->created_at->format('Y-m-d') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.students.edit', $student) }}"
                                           class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.students.destroy', $student) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('هل تريد حذف الطالب؟');">
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
                    <p class="p-4 text-center text-muted">
                        لا يوجد طلاب منشأون بعد.
                    </p>
                @endif
            </div>
            @if($students->hasPages())
                <div class="card-footer">
                    {{ $students->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
