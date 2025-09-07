@php use Carbon\Carbon; @endphp
@extends('layouts.app')
@section('title','إدارة الطلاب')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">إدارة الطلاب</h1>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('manager.students.create') }}" class="btn btn-outline-primary">
                    <i class="bi bi-plus-lg"></i> إضافة طالب جديد
                </a>

                <button type="button" class="btn btn-outline-primary"
                        data-bs-toggle="modal" data-bs-target="#requestCertificateModal">
                    <i class="bi bi-award"></i> طلب شهادة
                </button>
            </div>
        </div>

        {{-- Card للفلاتر --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <form method="GET" action="{{ route('manager.students.index') }}" class="row g-3 align-items-end">

                    {{-- نسبة حضور ≥ --}}
                    <div class="col-md-2">
                        <label class="form-label">حضور ≥ (%)</label>
                        <input type="number" name="attendance_min"
                               value="{{ request('attendance_min') }}"
                               min="0" max="100"
                               class="form-control form-control-sm">
                    </div>

                    {{-- علامة تسميع ≥ --}}
                    <div class="col-md-2">
                        <label class="form-label">تسميع ≥</label>
                        <input type="number" name="min_score"
                               value="{{ request('min_score') }}"
                               min="0"
                               class="form-control form-control-sm">
                    </div>

                    {{-- زر تطبيق / إعادة ضبط --}}
                    <div class="col-md-2 text-end">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-funnel-fill"></i> تطبيق
                        </button>
                    </div>

                    <div class="col-md-2 text-end">
                        <a href="{{ route('manager.students.index') }}"
                           class="btn btn-outline-secondary btn-sm w-100 mt-1">
                            إعادة ضبط
                        </a>
                    </div>
                </form>
            </div>

            {{-- جدول النتائج --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 text-end">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>رقم الهاتف</th>
                            <th>العنوان</th>
                            <th>تاريخ الميلاد</th>
                            <th>نسبة الحضور</th>
                            <th>أقصى تسميع</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $loop->iteration
                                + ($students->currentPage()-1)*$students->perPage() }}</td>
                                <td>
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </td>

                                <td>
                                    {{ $student->phone }}
                                </td> <td>
                                    {{ $student->address }}
                                </td>

                                <td>
                                    {{ $student->birthdate }}
                                </td>

                                <td>{{ $student->present_percentage }}%</td>
                                <td>
                                    {{
                                      $student->exams
                                        ->pluck('score')
                                        ->max()
                                        ?? '—'
                                    }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('manager.students.show',$student) }}"
                                       class="btn btn-sm btn-outline-info" title="عرض">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('manager.students.edit',$student) }}"
                                       class="btn btn-sm btn-outline-warning" title="تعديل">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('manager.students.destroy',$student) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('هل تريد حذف الطالب؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    لا توجد نتائج
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($students->hasPages())
                <div class="card-footer">
                    {{ $students->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal: طلب شهادة --}}
    <div class="modal fade" id="requestCertificateModal" tabindex="-1" aria-labelledby="reqCertLabel" aria-hidden="true" dir="rtl">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="reqCertLabel">طلب شهادة</h5>
                    <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>

                <form method="POST" action="{{ route('manager.certificates.store') }}" id="reqCertForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info small mb-3">
                            اختر الحلقة (من المواد المنتهية فقط)، ثم اختر الطالب من طلاب تلك الحلقة.
                        </div>

                        {{-- Class --}}
                        <div class="mb-3">
                            <label class="form-label">الحلقة</label>
                            <select id="reqClass" class="form-select" required>
                                <option value="">— اختر الحلقة —</option>
                            </select>
                            <div class="form-text">يتم تحميل الحلقات المنتهية تلقائيًا.</div>
                        </div>

                        {{-- Student --}}
                        <div class="mb-3">
                            <label class="form-label">الطالب</label>
                            <select id="reqStudent" name="student_id" class="form-select" disabled required>
                                <option value="">— اختر الطالب —</option>
                            </select>
                        </div>

                        {{-- Hidden subject_id (filled after class choice) --}}
                        <input type="hidden" name="subject_id" id="reqSubjectId">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success" id="reqSubmit" disabled>
                            <i class="bi bi-send"></i> إرسال الطلب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const modalEl    = document.getElementById('requestCertificateModal');
                const classSel   = document.getElementById('reqClass');
                const studentSel = document.getElementById('reqStudent');
                const subjInput  = document.getElementById('reqSubjectId');
                const submitBtn  = document.getElementById('reqSubmit');

                const CLASSES_URL  = @json(route('manager.certificates.ajax.finished-classes'));
                const STUDENTS_URL = @json(route('manager.certificates.ajax.class-students', ['class' => '__ID__']));

                function opt(el, value, text) {
                    const o = document.createElement('option');
                    o.value = value; o.textContent = text; return o;
                }

                function clearSelect(sel, placeholder='— اختر —') {
                    sel.innerHTML = '';
                    sel.appendChild(opt(sel, '', placeholder));
                }

                modalEl.addEventListener('shown.bs.modal', async () => {
                    submitBtn.disabled = true;
                    subjInput.value = '';
                    clearSelect(classSel, '— اختر الحلقة —');
                    clearSelect(studentSel, '— اختر الطالب —');
                    studentSel.disabled = true;

                    try {
                        const res = await fetch(CLASSES_URL, { headers: { 'Accept': 'application/json' }});
                        const data = await res.json(); // [{id,name,subject_id,subject_name}]
                        data.forEach(c => {
                            const o = opt(classSel, c.id, `${c.name} — ${c.subject_name} (#${c.subject_id})`);
                            o.dataset.subjectId = c.subject_id;
                            classSel.appendChild(o);
                        });
                    } catch (e) {
                        clearSelect(classSel, 'تعذّر تحميل الحلقات');
                    }
                });

                classSel.addEventListener('change', async (e) => {
                    const classId   = classSel.value;
                    const subjectId = classSel.selectedOptions[0]?.dataset?.subjectId || '';
                    subjInput.value = subjectId;

                    submitBtn.disabled = true;
                    clearSelect(studentSel, '— جاري التحميل —');
                    studentSel.disabled = true;

                    if (!classId) {
                        clearSelect(studentSel, '— اختر الطالب —');
                        return;
                    }
                    try {
                        const res = await fetch(STUDENTS_URL.replace('__ID__', classId), { headers: { 'Accept': 'application/json' }});
                        const data = await res.json(); // [{id, first_name, last_name}]
                        clearSelect(studentSel, '— اختر الطالب —');
                        data.forEach(s => studentSel.appendChild(opt(studentSel, s.id, `${s.first_name} ${s.last_name}`)));
                        studentSel.disabled = false;
                    } catch (e) {
                        clearSelect(studentSel, 'تعذّر تحميل الطلاب');
                    }
                });

                studentSel.addEventListener('change', () => {
                    submitBtn.disabled = !(studentSel.value && subjInput.value);
                });
            })();
        </script>
    @endpush

@endsection
