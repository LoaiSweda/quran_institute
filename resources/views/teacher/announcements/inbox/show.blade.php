@extends('layouts.app')
@section('title','عرض إعلان')

@section('content')
    <div class="container py-2">

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h3 class="mb-0 text-primary">
                    <i class="bi bi-megaphone-fill me-2"></i> {{ $ad->title }}
                </h3>
                <a href="{{ route('teacher.announcements.inbox') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> العودة للوارد
                </a>
            </div>

            @if($ad->image)
                <div class="text-center p-3">
                    <img src="{{ asset('storage/app/public/'.$ad->image) }}" alt="صورة الإعلان" class="img-fluid rounded" style="max-height:360px">
                </div>
            @endif

            <div class="card-body">
                <div class="row gy-3 text-end">
                    <div class="col-md-4">
                        <div class="text-muted small">النوع</div>
                        <div class="fw-semibold">{{ $ad->type?->name ?? '—' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">المعهد</div>
                        <div class="fw-semibold">{{ $ad->institute?->name ?? 'عام' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">ينتهي في</div>
                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($ad->end_date)->format('Y-m-d') }}</div>
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
