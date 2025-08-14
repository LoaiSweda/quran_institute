@extends('layouts.app')

@section('title', 'جميع جداول المواعيد')

@section('content')
    <div class="container-fluid">

        {{-- Header + Back --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">إدارة جداول المواعيد</h1>
            <a href="{{ route('admin.classes.index', ['institute_id'=>$inst->id]) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> العودة للحلقات
            </a>
        </div>

        {{-- Search & Filter Card --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <form method="GET" action="{{ route('admin.schedules.index', ['institute_id'=>$inst->id]) }}"
                      class="row gx-2 gy-2 align-items-center">

                    {{-- فلتر الحلقة --}}
                    <div class="col-md-2">
                        <select name="class_id" class="form-select form-select-sm">
                            <option value="">كل الحلقات</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>
                                {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- فلتر المادة --}}
                    <div class="col-md-2">
                        <select name="subject_id" class="form-select form-select-sm">
                            <option value="">كل المواد</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}" @selected(request('subject_id')==$sub->id)>
                                {{ $sub->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- فلتر اليوم --}}
                    <div class="col-md-2">
                        <select name="day_of_week" class="form-select form-select-sm">
                            <option value="">كل الأيام</option>
                            @foreach(['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'] as $day)
                                <option value="{{ $day }}" @selected(request('day_of_week')==$day)>
                                {{ $day }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- فلتر الفرز --}}
                    <div class="col-md-2">
                        <select name="sort" class="form-select form-select-sm">
                            <option value="">فرز حسب</option>
                            <option value="day"     @selected(request('sort')=='day')>اليوم</option>
                            <option value="start"   @selected(request('sort')=='start')>الوقت</option>
                            <option value="teacher" @selected(request('sort')=='teacher')>الأستاذ</option>
                        </select>
                    </div>

                    {{-- فلتر الاتجاه --}}
                    <div class="col-md-2">
                        <select name="direction" class="form-select form-select-sm">
                            <option value="asc"  @selected(request('direction')=='asc')>تصاعدي</option>
                            <option value="desc" @selected(request('direction')=='desc')>تنازلي</option>
                        </select>
                    </div>

                    {{-- الأزرار --}}
                    <div class="col-md-2 text-md-end">
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-funnel-fill"></i> تطبيق
                        </button>
                        <a href="{{ route('admin.schedules.index', ['institute_id'=>$inst->id]) }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-counterclockwise"></i> إعادة
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 text-end">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الحلقة</th>
                            <th>المادة</th>
                            <th>المدرِّس</th>
                            <th>اليوم</th>
                            <th>من</th>
                            <th>إلى</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($schedules as $i => $sch)
                            <tr>
                                <td>{{ $i + 1 + ($schedules->perPage() * ($schedules->currentPage() - 1)) }}</td>
                                <td>{{ $sch->educationClass->name }}</td>
                                <td>{{ $sch->educationClass->subject->name }}</td>
                                <td>
                                    {{ optional($sch->educationClass->teacher)->first_name }}
                                    {{ optional($sch->educationClass->teacher)->last_name }}
                                </td>
                                <td>{{ $sch->day_of_week }}</td>
                                <td>{{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">لا توجد جداول مواعيد لعرضها.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($schedules->hasPages())
                <div class="card-footer">
                    {{ $schedules->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
