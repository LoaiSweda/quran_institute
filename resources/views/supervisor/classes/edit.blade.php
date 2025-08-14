@extends('layouts.app')
@section('title',"تعديل الحلقة — {$class->name}")

@section('content')
    <div class="container-fluid">

        {{-- Header + Back --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">تعديل الحلقة</h1>
            <a href="{{ route('admin.classes.index', ['institute_id'=>$inst->id]) }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> رجوع
            </a>
        </div>

        {{-- Form Card --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">بيانات الحلقة</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.classes.update', ['class'=>$class->id, 'institute_id'=>$inst->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- اسم الحلقة --}}
                    <div class="mb-3">
                        <label class="form-label">اسم الحلقة</label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $class->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- المادة --}}
                    <div class="mb-3">
                        <label class="form-label">المادة</label>
                        <select name="subject_id"
                                class="form-select @error('subject_id') is-invalid @enderror"
                                required>
                            <option value="">اختر المادة</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}" @selected(old('subject_id', $class->subject_id) == $sub->id)>
                                {{ $sub->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- المدرّس --}}
                    <div class="mb-3">
                        <label class="form-label">اسم المدرّس</label>
                        <select name="user_id"
                                class="form-select @error('user_id') is-invalid @enderror"
                                required>
                            <option value="">اختر المدرّس</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" @selected(old('user_id', $class->user_id) == $t->id)>
                                {{ $t->first_name }} {{ $t->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- عدد الطلاب --}}
                    <div class="mb-3">
                        <label class="form-label">عدد الطلاب</label>
                        <input type="number" name="students_count"
                               class="form-control @error('students_count') is-invalid @enderror"
                               value="{{ old('students_count', $class->students_count) }}"
                               min="1" required>
                        @error('students_count')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="text-start">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Session Schedules Card (روابط إدارة المواعيد للمشرف إن كانت المسارات مضافة) --}}
        <div class="card shadow-sm">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">جداول مواعيد الحلقة</h6>
                <a href="{{ route('admin.classes.schedules.create', ['class'=>$class->id, 'institute_id'=>$inst->id]) }}"
                   class="btn btn-sm btn-success">
                    <i class="bi bi-plus-lg"></i> إضافة موعد جديد
                </a>
            </div>
            <div class="card-body">
                @if($class->sessionSchedules->isEmpty())
                    <p>لا توجد جداول مواعيد حتى الآن.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>اليوم</th>
                                <th>وقت البدء</th>
                                <th>وقت الانتهاء</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($class->sessionSchedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->day_of_week }}</td>
                                    <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.classes.schedules.edit', ['class'=>$class->id, 'schedule'=>$schedule->id, 'institute_id'=>$inst->id]) }}"
                                           class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i> تعديل
                                        </a>
                                        <form action="{{ route('admin.classes.schedules.destroy', ['class'=>$class->id, 'schedule'=>$schedule->id, 'institute_id'=>$inst->id]) }}"
                                              method="POST" class="d-inline-block"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الموعد؟');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> حذف
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
