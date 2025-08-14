@extends('layouts.app')
@section('title','عرض إعلان')

@section('content')
    <div class="container py-2">

        <div class="card shadow-sm border-0">
            {{-- Header --}}
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <div>
                    <h3 class="mb-0 text-primary">
                        <i class="bi bi-megaphone-fill me-2"></i> {{ $ad->title }}
                    </h3>
                    <div class="small text-muted mt-1">
                        النوع: <span class="fw-semibold">{{ $ad->type?->name ?? '—' }}</span>
                        <span class="mx-2">|</span>
                        المعهد: <span class="fw-semibold">{{ $ad->institute?->name ?? 'عام' }}</span>
                        <span class="mx-2">|</span>
                        الناشر: <span class="fw-semibold">{{ $ad->publisher?->email ?? '—' }}</span>
                        @if(optional($ad->publisher?->role)->name)
                            <span class="badge bg-light text-dark ms-1">{{ $ad->publisher->role->name }}</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('manager.announcements.inbox') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> العودة للوارد
                </a>
            </div>

            @if($ad->image)
                <div class="text-center p-3">
                    <img src="{{ asset('storage/'.$ad->image) }}" alt="صورة الإعلان" class="img-fluid rounded" style="max-height:360px">
                </div>
            @endif

            <div class="card-body">
                <div class="row gy-3 text-end">
                    <div class="col-md-4">
                        <div class="text-muted small">ينتهي في</div>
                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($ad->end_date)->format('Y-m-d') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">الحالة</div>
                        @php $st = $ad->computed_status ?? ($ad->status); @endphp
                        @if($st === 'expired')
                            <span class="badge bg-secondary">منتهي</span>
                        @elseif($st === 'active')
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-danger">غير نشط</span>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">المشاهِدون المسموح لهم</div>
                        <div>
                            @forelse($ad->userAds as $ua)
                                <span class="badge bg-info text-dark ms-1">{{ $ua->watches_role }}</span>
                            @empty
                                <span class="text-muted">— لا يوجد —</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="text-muted small mb-1">الوصف</div>
                        <div class="border rounded p-3">{{ $ad->description ?: '— لا يوجد وصف —' }}</div>
                    </div>

                    @if($ad->link)
                        <div class="col-12 text-end">
                            <a href="{{ $ad->link }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="bi bi-link-45deg"></i> فتح الرابط
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection
