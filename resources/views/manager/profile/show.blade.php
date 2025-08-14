@extends('layouts.app')
@section('title','بروفايل المدير')

@section('content')
    <style>
        .profile-cover {
            background: linear-gradient(135deg, #4f46e5, #06b6d4);
            border-radius: 1rem;
            padding: 2.25rem;
            color: #fff;
        }
        .profile-avatar {
            width: 84px; height: 84px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255,255,255,0.25);
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 28px; color: #fff;
        }
        .profile-card { border-radius: 1rem; }
        .badge-soft { background: rgba(13,110,253,0.12); color: #0d6efd; }
    </style>

    @php
        // اجلب الاسم الكامل من أول بروفايل متوفّر
        $fullName = trim(collect([
            optional($user->adminProfile)->first_name.' '.optional($user->adminProfile)->last_name,
            optional($user->teacher)->first_name.' '.optional($user->teacher)->last_name,
            optional($user->student)->first_name.' '.optional($user->student)->last_name,
            $user->name,
            ($user->email ? explode('@',$user->email)[0] : null),
            'مستخدم'
        ])->first(fn($v) => $v && trim($v) !== ''));

        // أحرف الأفاتار
        $baseForInitials = $fullName ?: ($user->email ? explode('@',$user->email)[0] : 'U');
        $parts = preg_split('/\s+/u', trim($baseForInitials));
        $initials = mb_strtoupper(mb_substr($parts[0] ?? 'U',0,1).mb_substr($parts[1] ?? '',0,1));
    @endphp

    <div class="container-fluid">

        {{-- Cover --}}
        <div class="profile-cover mb-4 shadow-sm">
            <div class="d-flex align-items-center gap-3 flex-wrap justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="profile-avatar">{{ $initials }}</div>
                    <div class="text-end">
                        <div class="fs-4 fw-bold mb-1">{{ $fullName }}</div>
                        <div class="small">
                        <span class="badge badge-soft">
                            <i class="bi bi-shield-lock"></i>
                            {{ $user->role->name ?? '—' }}
                        </span>
                        </div>
                    </div>
                </div>
                <div class="text-end small">
                    <div><i class="bi bi-envelope"></i> {{ $user->email }}</div>
                    <div class="opacity-75">انضمّ في: {{ optional($user->created_at)->format('Y-m-d') }}</div>
                </div>
            </div>
        </div>

        {{-- Personal & Institute --}}
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card profile-card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h6 class="m-0 fw-semibold"><i class="bi bi-person-circle"></i> بياناتي</h6>
                    </div>
                    <div class="card-body text-end">
                        <div class="mb-3">
                            <div class="text-muted small">الاسم الكامل</div>
                            <div class="fw-semibold">{{ $fullName }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">البريد الإلكتروني</div>
                            <div class="fw-semibold">{{ $user->email }}</div>
                        </div>
                        <div class="mb-0">
                            <div class="text-muted small">الدور</div>
                            <div class="fw-semibold">{{ $user->role->name ?? '—' }}</div>
                        </div>
                        {{-- تمت إزالة زر "تعديل معلوماتي" وفق طلبك --}}
                    </div>
                </div>
            </div>

            {{-- Institute --}}
            <div class="col-lg-6">
                <div class="card profile-card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h6 class="m-0 fw-semibold"><i class="bi bi-building"></i> المعهد المُدار</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-3 align-items-center">
                            @if($inst->image)
                                <img src="{{ asset('storage/'.$inst->image) }}"
                                     class="rounded" style="width:72px;height:72px;object-fit:cover" alt="Institute">
                            @else
                                <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                     style="width:72px;height:72px;">
                                    <i class="bi bi-building fs-3 text-secondary"></i>
                                </div>
                            @endif
                            <div class="text-end">
                                <div class="fw-bold fs-5">{{ $inst->name }}</div>
                                <div class="text-muted small"><i class="bi bi-geo-alt"></i> {{ $inst->address ?? '—' }}</div>
                                <div class="mt-2">
                                    <a href="{{ route('manager.classes.index') }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-grid"></i> الانتقال لإدارة الحلقات
                                    </a>
                                    <a href="{{ url('/manager/subjects') }}" class="btn btn-sm btn-outline-secondary">المواد</a>
                                    <a href="{{ url('/manager/teachers') }}" class="btn btn-sm btn-outline-secondary">المدرّسون</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
