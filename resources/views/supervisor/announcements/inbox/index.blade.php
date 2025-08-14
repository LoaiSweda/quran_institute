@extends('layouts.app')
@section('title','إعلانات موجّهة للمشرف')

@section('content')
    <div class="container-fluid">

        {{-- Header + Institute Filter --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 text-gray-800"><i class="bi bi-inbox"></i> الإعلانات الموجّهة إليّ (مشرف)</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.announcements.index', request()->only('institute_id')) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-bullhorn"></i> إعلاناتي (أنا الناشر)
                </a>
                <a href="{{ url('/admin/dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> العودة
                </a>
            </div>
        </div>

        {{-- (اختياري) تبديل المعهد عبر بارامتر --}}
        @php
            // لو مرّرت مسبقًا لصفحتك قائمة معاهد المشرف $myInstitutes = auth()->user()->institutes()->select('institutes.id','institutes.name')->get();
            $myInstitutes = auth()->user()->institutes()->select('institutes.id','institutes.name')->get();
            $currentInstId = (int) request('institute_id', (int) optional($myInstitutes->first())->id);
        @endphp
        @if($myInstitutes->count() > 1)
            <div class="card shadow-sm mb-3">
                <div class="card-body py-2">
                    <form method="GET" action="{{ route('admin.announcements.inbox') }}" class="row g-2 align-items-center">
                        <div class="col-auto">
                            <label class="col-form-label">اختر المعهد:</label>
                        </div>
                        <div class="col-auto">
                            <select name="institute_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                @foreach($myInstitutes as $inst)
                                    <option value="{{ $inst->id }}" @selected($currentInstId==$inst->id)>{{ $inst->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-check2-circle"></i> تطبيق
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Table --}}
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">الوارد</h6>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 text-end align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>النوع</th>
                            <th>المعهد</th>
                            <th>الناشر</th>
                            <th>الوصف المختصر</th>
                            <th>ينتهي في</th>
                            <th class="text-center">عرض</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($ads as $ad)
                            <tr>
                                <td>{{ $loop->iteration + ($ads->currentPage()-1)*$ads->perPage() }}</td>
                                <td class="fw-semibold">{{ $ad->title }}</td>
                                <td>{{ $ad->type?->name ?? '—' }}</td>
                                <td>{{ $ad->institute?->name ?? 'عام' }}</td>
                                <td>
                                    {{ $ad->publisher?->email ?? '—' }}
                                    @if(optional($ad->publisher?->role)->name)
                                        <span class="badge bg-light text-dark ms-1">{{ $ad->publisher->role->name }}</span>
                                    @endif
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($ad->description, 70) }}</td>
                                <td>{{ \Carbon\Carbon::parse($ad->end_date)->format('Y-m-d') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.announcements.inbox.show', array_merge(['ad'=>$ad->id], request()->only('institute_id'))) }}"
                                       class="btn btn-sm btn-outline-primary" title="عرض">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    لا توجد إعلانات موجّهة لك حالياً.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($ads->hasPages())
                <div class="card-footer">
                    {{ $ads->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
