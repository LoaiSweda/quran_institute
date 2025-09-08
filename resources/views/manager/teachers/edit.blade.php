@extends('layouts.app')
@section('title',"تعديل بيانات المدرس — {$teacher->first_name} {$teacher->last_name}")

@section('content')
    <div class="container-fluid">
        {{-- العنوان وأزرار --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">تعديل بيانات المدرّس</h1>
            <div>
                <a href="{{ route('manager.teachers.show', $teacher) }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> عرض
                </a>
                <a href="{{ route('manager.teachers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list"></i> قائمة المدرّسين
                </a>
            </div>
        </div>

        {{-- رسالة نجاح --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- بطاقة التعديل --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('manager.teachers.update', $teacher) }}"
                      method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-md-3 text-center">
                        <label class="form-label d-block">الصورة الحالية</label>
                        @if($teacher->image)
                            <img src="{{ asset('storage/app/public/'.$teacher->image) }}"
                                 alt="صورة المدرّس"
                                 class="rounded-circle mb-2"
                                 style="width:120px;height:120px;object-fit:cover;">
                        @else
                            <div class="text-muted mb-2">لا توجد صورة</div>
                        @endif

                        <label class="form-label mt-2 d-block">تغيير الصورة</label>
                        <input type="file" name="image"
                               class="form-control form-control-sm @error('image') is-invalid @enderror">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-9">
                        <div class="row g-3">
                            <div class="col-md-6 text-end">
                                <label class="form-label">الاسم الأول<span class="text-danger">*</span></label>
                                <input type="text" name="first_name"
                                       class="form-control form-control-sm @error('first_name') is-invalid @enderror"
                                       value="{{ old('first_name', $teacher->first_name) }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 text-end">
                                <label class="form-label">اسم العائلة<span class="text-danger">*</span></label>
                                <input type="text" name="last_name"
                                       class="form-control form-control-sm @error('last_name') is-invalid @enderror"
                                       value="{{ old('last_name', $teacher->last_name) }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 text-end">
                                <label class="form-label">الهاتف</label>
                                <input type="text" name="phone"
                                       class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $teacher->phone) }}">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 text-end">
                                <label class="form-label">العنوان</label>
                                <input type="text" name="address"
                                       class="form-control form-control-sm @error('address') is-invalid @enderror"
                                       value="{{ old('address', $teacher->address) }}">
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 text-end">
                                <label class="form-label">تاريخ الميلاد</label>
                                <input type="date" name="birthdate"
                                       class="form-control form-control-sm @error('birthdate') is-invalid @enderror"
                                       value="{{ old('birthdate', $teacher->birthdate ? $teacher->birthdate->format('Y-m-d') : '') }}">
                                @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 text-end">
                                <label class="form-label">المستخدم المرتبط</label>
                                <div class="form-control form-control-sm text-start">
                                    {{ optional($teacher->user)->name ?? '—' }}
                                    <div class="small text-muted">{{ optional($teacher->user)->email ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- أزرار الحفظ --}}
                    <div class="col-12 text-end mt-3">
                        <a href="{{ route('manager.teachers.show', $teacher) }}" class="btn btn-secondary">
                            إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- قائمة المدرّسين المنشأين سابقًا (أسفل الصفحة) --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">المدرّسون المرتبطون بالمعهد</h5>
                <small class="text-muted">إجمالي: {{ $teachers->count() ?? 0 }}</small>
            </div>
            <div class="card-body p-0">
                @if(isset($teachers) && $teachers->isNotEmpty())
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
                                    <td>{{ $t->first_name }} {{ $t->last_name }}</td>
                                    <td>{{ optional($t->user)->email ?? '—' }}</td>
                                    <td>{{ $t->phone ?? '—' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('manager.teachers.show', $t->id) }}" class="btn btn-sm btn-outline-info">عرض</a>
                                        <a href="{{ route('manager.teachers.edit', $t->id) }}" class="btn btn-sm btn-outline-warning">تعديل</a>
                                        <form action="{{ route('manager.teachers.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل تريد فصل هذا المدرّس عن المعهد؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">فصل</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center text-muted">لا يوجد مدرسون مرتبطون بهذا المعهد.</div>
                @endif
            </div>
        </div>

    </div>
@endsection
