@extends('layouts.app')

@section('title','تفاصيل الإعلان')

@section('content')
    <div class="container py-2">

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h3 class="mb-0 text-primary"><i class="bi bi-megaphone-fill me-2"></i>تفاصيل الإعلان</h3>
                <div>
                    <a href="{{ route('manager.announcements.edit', $ad) }}" class="btn btn-sm btn-outline-warning me-2">
                        <i class="bi bi-pencil"></i> تعديل
                    </a>
                    <form action="{{ route('manager.announcements.destroy', $ad) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('هل تريد حقًا حذف هذا الإعلان؟');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i> حذف
                        </button>
                    </form>
                </div>
            </div>

            @if($ad->image)
                <div class="text-center mb-4">
                    <img src="{{ asset('storage/app/public/'.$ad->image) }}" alt="صورة الإعلان" class="img-fluid rounded">
                </div>
            @endif>

            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-lg-6">
                        <h5 class="text-secondary">العنوان</h5>
                        <p class="fs-5">{{ $ad->title }}</p>
                    </div>
                    <div class="col-lg-6">
                        <h5 class="text-secondary">النوع</h5>
                        <p>{{ $ad->type?->name ?? '—' }}</p>
                    </div>
                    <div class="col-lg-6">
                        <h5 class="text-secondary">تاريخ الانتهاء</h5>
                        <p>{{ \Carbon\Carbon::parse($ad->end_date)->translatedFormat('Y-m-d') }}</p>
                    </div>
                    <div class="col-lg-6">
                        <h5 class="text-secondary">الحالة</h5>
                        @if($ad->computed_status === 'expired')
                            <span class="badge bg-secondary">منتهي</span>
                        @elseif($ad->computed_status === 'active')
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-danger">غير نشط</span>
                        @endif
                    </div>
                    <div class="col-12">
                        <h5 class="text-secondary">الوصف</h5>
                        <p class="border p-3 rounded">{{ $ad->description ?: '— لا يوجد وصف —' }}</p>
                    </div>
                    @if($ad->link)
                        <div class="col-12">
                            <h5 class="text-secondary">الرابط</h5>
                            <a href="{{ $ad->link }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="bi bi-link-45deg"></i> فتح الرابط
                            </a>
                        </div>
                    @endif
                    <div class="col-12">
                        <h5 class="text-secondary">مشاهدو الإعلان</h5>
                        <div>
                            @forelse($ad->userAds as $ua)
                                <span class="badge bg-info text-dark me-1">{{ ucfirst($ua->watches_role) }}</span>
                            @empty
                                <span class="text-muted">— لا يوجد مشاهِدون —</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white text-center">
                <a href="{{ route('manager.announcements.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> العودة إلى القائمة
                </a>
            </div>
        </div>

    </div>
@endsection
