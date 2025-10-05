{{-- resources/views/manager/students/edit.blade.php --}}
@extends('layouts.app')
@section('title','تعديل بيانات الطالب')

@section('content')
    <div class="container-fluid">
        {{-- Header + Back --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 text-gray-800">
                تعديل {{ $student->first_name }} {{ $student->last_name }}
            </h2>
            <a href="{{ route('manager.students.index') }}"
               class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> رجوع
            </a>
        </div>
        <div class="card shadow-sm p-4">
            <form action="{{ route('manager.students.update', $student) }}"
                  method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- عرض الصورة الحالية --}}
                @if($student->image)
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">الصورة الحالية</label>
                            <div>
                                <img src="{{ Storage::url($student->image) }}" alt="صورة الطالب"
                                     style="max-width: 200px; max-height: 200px;" class="img-thumbnail">
                            </div>
                        </div>
                    </div>
                @endif

                {{-- حقل الصورة الجديدة --}}
                <div class="col-md-6">
                    <label class="form-label">صورة جديدة (اختياري)</label>
                    <input type="file" name="image"
                           class="form-control form-control-sm @error('image') is-invalid @enderror"
                           accept="image/*">
                    @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- QR Code --}}
                <div class="col-md-6">
                    <label class="form-label">كود QR</label>
                    <div class="text-center">
                        <div class="border rounded p-3 bg-light d-inline-block mb-2">
                            {!! QrCode::size(120)->generate($student->qr) !!}
                        </div>
                        <input type="text"
                               class="form-control form-control-sm"
                               value="{{ $student->qr }}"
                               readonly>
                        <small class="text-muted">يمكن مسح هذا الكود ضوئياً</small>
                    </div>
                </div>

                {{-- البيانات الأساسية --}}
                <div class="col-md-4">
                    <label class="form-label">الاسم الأول</label>
                    <input type="text" name="first_name"
                           value="{{ old('first_name', $student->first_name) }}"
                           class="form-control form-control-sm @error('first_name') is-invalid @enderror">
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">اسم العائلة</label>
                    <input type="text" name="last_name"
                           value="{{ old('last_name', $student->last_name) }}"
                           class="form-control form-control-sm @error('last_name') is-invalid @enderror">
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">تاريخ الميلاد</label>
                    <input type="date" name="birthdate"
                           value="{{ old('birthdate', $student->birthdate) }}"
                           class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الهاتف</label>
                    <input type="text" name="phone"
                           value="{{ old('phone', $student->phone) }}"
                           class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address"
                           value="{{ old('address', $student->address) }}"
                           class="form-control form-control-sm">
                </div>

                <div class="col-md-6">
                    <label class="form-label">اسم الأب</label>
                    <input type="text" name="father_name"
                           value="{{ old('father_name', $student->father_name) }}"
                           class="form-control form-control-sm">
                </div>

                {{-- عمل الأب --}}
                <div class="col-md-6">
                    <label class="form-label">عمل الأب</label>
                    <input type="text" name="father_job" value="{{ old('father_job', $student->father_job) }}"
                           class="form-control form-control-sm">
                </div>

                {{-- عمل الأم --}}
                <div class="col-md-6">
                    <label class="form-label">عمل الأم</label>
                    <input type="text" name="mother_job" value="{{ old('mother_job', $student->mother_job) }}"
                           class="form-control form-control-sm">
                </div>

                {{-- اسم المدرسة --}}
                <div class="col-md-6">
                    <label class="form-label">اسم المدرسة</label>
                    <input type="text" name="school_name" value="{{ old('school_name', $student->school_name) }}"
                           class="form-control form-control-sm">
                </div>

                {{-- الحالة المادية --}}
                <div class="col-md-6">
                    <label class="form-label">الحالة المادية</label>
                    <select name="financial_status" class="form-select form-select-sm bg-white text-dark">
                        <option value="">-- اختر الحالة المادية --</option>
                        <option value="ممتاز" {{ old('financial_status', $student->financial_status) == 'ممتاز' ? 'selected' : '' }}>ممتاز</option>
                        <option value="متوسط" {{ old('financial_status', $student->financial_status) == 'متوسط' ? 'selected' : '' }}>متوسط</option>
                        <option value="ضعيف" {{ old('financial_status', $student->financial_status) == 'ضعيف' ? 'selected' : '' }}>ضعيف</option>
                    </select>
                </div>

                {{-- الحالة الصحية --}}
                <div class="col-12">
                    <label class="form-label">الحالة الصحية</label>
                    <textarea name="health_status" rows="2" class="form-control form-control-sm">{{ old('health_status', $student->health_status) }}</textarea>
                </div>

                {{-- عدد الأجزاء المحفوظة --}}
                <div class="col-md-4">
                    <label class="form-label">عدد الأجزاء المحفوظة</label>
                    <input type="number" name="memorized_parts" value="{{ old('memorized_parts', $student->memorized_parts) }}"
                           min="0" max="60"
                           class="form-control form-control-sm">
                </div>

                {{-- هل لديه أخ مسجل --}}
                <div class="col-md-4">
                    <label class="form-label">هل لديه أخ مسجل في الدورة؟</label>
                    <div class="mt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="has_sibling" id="has_sibling_yes"
                                   value="1" {{ old('has_sibling', $student->has_sibling) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_sibling_yes">نعم</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="has_sibling" id="has_sibling_no"
                                   value="0" {{ old('has_sibling', $student->has_sibling) == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_sibling_no">لا</label>
                        </div>
                    </div>
                </div>

                {{-- عدد الإخوة --}}
                <div class="col-md-4">
                    <label class="form-label">عدد الإخوة المسجلين</label>
                    <input type="number" name="siblings_count" value="{{ old('siblings_count', $student->siblings_count) }}"
                           min="0"
                           class="form-control form-control-sm">
                </div>

                {{-- قائمة الوصيّين --}}
                <div class="col-md-6">
                    <label class="form-label">الوصي</label>
                    <select name="guardian_id"
                            class="form-select form-select-sm bg-white text-dark">
                        {{-- خيار فارغ للـ null --}}
                        <option value="" {{ old('guardian_id', $student->guardian_id)=='' ? 'selected' : '' }}>
                            -- بدون وصي --
                        </option>
                        @foreach($guardians as $g)
                            <option value="{{ $g->id }}"
                                {{ old('guardian_id', $student->guardian_id)== $g->id ? 'selected' : '' }}>
                                {{ $g->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('guardian_id')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                {{-- زر التحديث --}}
                <div class="col-12 text-center mt-3">
                    <button class="btn btn-warning">
                        <i class="bi bi-save"></i> تحديث بيانات الطالب
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
