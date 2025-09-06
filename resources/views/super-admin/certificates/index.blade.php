@extends('layouts.app')
@section('title','طلبات الشهادات — المشرف العام')

@section('content')
    <div class="container-fluid" dir="rtl">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">طلبات الشهادات</h1>
        </div>

        {{-- Filters --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('super-admin.certificates.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">الكل</option>
                            <option value="pending"  @selected(request('status')==='pending')>قيد المراجعة ({{ $counts['pending'] ?? 0 }})</option>
                            <option value="approved" @selected(request('status')==='approved')>معتمدة ({{ $counts['approved'] ?? 0 }})</option>
                            <option value="refused"  @selected(request('status')==='refused')>مرفوضة ({{ $counts['refused'] ?? 0 }})</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">المادة</label>
                        <select name="subject_id" class="form-select form-select-sm">
                            <option value="">الكل</option>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}" @selected(request('subject_id')==$s->id)>
                                    {{ $s->name }} (#{{ $s->id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">بحث (اسم/إيميل)</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="بحث...">
                    </div>

                    <div class="col-md-3 text-end">
                        <button class="btn btn-outline-primary btn-sm w-100" type="submit">
                            <i class="bi bi-search"></i> تطبيق
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 text-end">
                        <thead class="table-light">
                        <tr>
                            <th>الطالب</th>
                            <th>الإيميل</th>
                            <th>المادة</th>
                            <th>الحالة</th>
                            <th>تاريخ التقديم</th>
                            <th>المراجع</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td>{{ optional($req->student)->first_name }} {{ optional($req->student)->last_name }}</td>
                                <td>{{ optional(optional($req->student)->user)->email ?? '—' }}</td>
                                <td>{{ optional($req->subject)->name }} @if($req->subject) <span class="text-muted">(#{{ $req->subject->id }})</span> @endif</td>
                                <td>
                                    @switch($req->status)
                                        @case('approved') <span class="badge bg-success">معتمدة</span> @break
                                        @case('refused')  <span class="badge bg-danger">مرفوضة</span> @break
                                        @default           <span class="badge bg-secondary">قيد المراجعة</span>
                                    @endswitch
                                </td>
                                <td>{{ optional($req->request_at)->format('Y-m-d H:i') }}</td>
                                <td>
                                    {{ optional($req->reviewer)->name ?? '—' }}
                                    @if($req->reviewed_at)
                                        <div class="small text-muted">{{ $req->reviewed_at->format('Y-m-d H:i') }}</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($req->status === 'pending')
                                        {{-- زر يفتح المودال لاعتماد الطلب --}}
                                        <button type="button"
                                                class="btn btn-success btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#decisionModal"
                                                data-action="{{ route('super-admin.certificates.approve', $req) }}"
                                                data-kind="approve"
                                                data-student="{{ optional($req->student)->first_name }} {{ optional($req->student)->last_name }}"
                                                data-subject="{{ optional($req->subject)->name }}">
                                            <i class="bi bi-check2-circle"></i> اعتماد
                                        </button>

                                        {{-- زر يفتح المودال لرفض الطلب --}}
                                        <button type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#decisionModal"
                                                data-action="{{ route('super-admin.certificates.refuse', $req) }}"
                                                data-kind="refuse"
                                                data-student="{{ optional($req->student)->first_name }} {{ optional($req->student)->last_name }}"
                                                data-subject="{{ optional($req->subject)->name }}">
                                            <i class="bi bi-x-circle"></i> رفض
                                        </button>
                                    @else
                                        <button class="btn btn-outline-secondary btn-sm" disabled>
                                            <i class="bi bi-slash-circle"></i> تم اتخاذ القرار
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">لا توجد طلبات.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($requests->hasPages())
                <div class="card-footer">
                    {{ $requests->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal: تأكيد اعتماد/رفض --}}
    <div class="modal fade" id="decisionModal" tabindex="-1" aria-hidden="true" dir="rtl">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="decisionModalLabel">تأكيد الإجراء</h5>
                    <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>

                <form method="POST" id="decisionForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-1">
                            هل تريد <span id="decisionKindText" class="fw-bold"></span> طلب الشهادة؟
                        </p>
                        <div class="small text-muted">
                            الطالب: <span id="studentName" class="fw-semibold"></span><br>
                            المادة: <span id="subjectName" class="fw-semibold"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" id="decisionSubmit" class="btn">تأكيد</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
{{-- Script لحقن القيم في المودال --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('decisionModal');
        modalEl.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;

            // البيانات القادمة من الزر
            const action  = btn.getAttribute('data-action');
            const kind    = btn.getAttribute('data-kind'); // approve | refuse
            const student = btn.getAttribute('data-student') || '—';
            const subject = btn.getAttribute('data-subject') || '—';

            // عناصر داخل المودال
            const form         = modalEl.querySelector('#decisionForm');
            const submitBtn    = modalEl.querySelector('#decisionSubmit');
            const kindTextSpan = modalEl.querySelector('#decisionKindText');
            const studentSpan  = modalEl.querySelector('#studentName');
            const subjectSpan  = modalEl.querySelector('#subjectName');

            // ضبط الفورم
            form.setAttribute('action', action);

            // نصوص وألوان حسب النوع
            if (kind === 'approve') {
                kindTextSpan.textContent = 'اعتماد';
                submitBtn.textContent = 'اعتماد';
                submitBtn.className = 'btn btn-success';
            } else {
                kindTextSpan.textContent = 'رفض';
                submitBtn.textContent = 'رفض';
                submitBtn.className = 'btn btn-outline-danger';
            }

            // الأسماء
            studentSpan.textContent = student;
            subjectSpan.textContent = subject;
        });
    });
</script>
@endpush
