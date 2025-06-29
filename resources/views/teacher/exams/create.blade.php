{{-- resources/views/teacher/exams/create.blade.php --}}
@extends('layouts.app')

@section('title', "إضافة امتحان لـ {$student->student->first_name} {$student->student->last_name}")

@section('content')
<div class="container-fluid">

    {{-- عرض رسالة النجاح إن وجدت --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="content-header">
        <h2>إضافة امتحان / تسميع</h2>
        <button id="toggleForm" class="btn-open">
            <i class="bi bi-journal-plus"></i> إضافة امتحان جديد
        </button>
    </div>

    {{-- form مخفي افتراضياً --}}
    <div id="inlineForm" class="announcement-form mb-4">
        <h3>نموذج إضافة امتحان</h3>
        <form action="{{ route('teacher.classes.students.exams.store', ['class'=>$class->id,'student'=>$student->id]) }}"
              method="POST">
            @csrf
            <div class="form-group">
                <label for="name">اسم الامتحان *</label>
                <input type="text" name="name" id="name"
                       class="@error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="notes">ملاحظات</label>
                <textarea name="notes" id="notes"
                          class="@error('notes') is-invalid @enderror"
                          rows="3">{{ old('notes') }}</textarea>
                @error('notes') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <div class="form-group half">
                    <label for="points">نقاط الامتحان *</label>
                    <input type="number" name="points" id="points"
                           class="@error('points') is-invalid @enderror"
                           value="{{ old('points') }}" required>
                    @error('points') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group half">
                    <label for="degree">الدرجة *</label>
                    <input type="number" name="degree" id="degree"
                           class="@error('degree') is-invalid @enderror"
                           value="{{ old('degree') }}" required>
                    @error('degree') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <button type="submit" class="btn-submit">حفظ الامتحان</button>
        </form>
    </div>

    @php
        $studentProfile = $student->student;
    @endphp

    <div class="card shadow-sm">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">الامتحانات المسجّلة للطالب</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                @if($studentProfile->exams()->count() === 0)
                    <p class="text-center text-muted py-4 mb-0">لا توجد امتحانات مسجّلة بعد.</p>
                @else
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>اسم الامتحان</th>
                                <th>النقاط</th>
                                <th>الدرجة</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentProfile->exams()->latest()->get() as $idx => $exam)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $exam->name }}</td>
                                    <td>{{ $exam->points }}</td>
                                    <td>{{ $exam->degree }}</td>
                                    <td>{{ $exam->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


@push('styles')
<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.btn-open {
    padding: 8px 16px;
    background: #2980b9;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-open:hover { background: #1f5d8a; }

.announcement-form {
    display: none;
    background: #fff;
    padding: 20px 25px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}
.announcement-form h3 {
    margin-bottom: 15px;
    color: #2c3e50;
    text-align: center;
    font-size: 1.4rem;
}
.form-row { display: flex; gap: 20px; }
.form-group { margin-bottom: 16px; flex: 1; }
.form-group.half { flex: 1; }
.form-group label { display: block; margin-bottom: 6px; color: #34495e; font-weight: 500; }
.form-group input, .form-group textarea {
    width: 100%; padding: 10px 12px; border: 1px solid #bdc3c7; border-radius: 6px; font-size: .95rem;
    transition: border-color .2s;
}
.form-group input:focus, .form-group textarea:focus { border-color: #2980b9; outline: none; }
.btn-submit {
    background: #27ae60; color: #fff; border: none; padding: 10px 18px; border-radius: 6px;
    cursor: pointer; width: 50%; margin: 0 auto; display: block; transition: background .2s;
}
.btn-submit:hover { background: #1e8449; }
.error { color: #e74c3c; font-size: .85rem; margin-top: 4px; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('toggleForm');
    const form   = document.getElementById('inlineForm');
    toggle.addEventListener('click', () => {
        const isHidden = form.style.display === 'none' || !form.style.display;
        form.style.display = isHidden ? 'block' : 'none';
        toggle.textContent = isHidden ? 'إلغاء' : 'إضافة امتحان جديد';
    });
});
</script>
@endpush
