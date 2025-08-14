@extends('layouts.app')
@section('title','بروفايل المعلّم')

@section('content')
    <style>
        .profile-cover {
            background: linear-gradient(135deg, #0ea5e9, #22c55e);
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
        .institute-pill {
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 999px; padding: .25rem .75rem;
            display: inline-flex; align-items: center; gap: .5rem;
            background-color: #fff;
        }
        .institute-pill.active { border-color: #0d6efd; background: rgba(13,110,253,0.06); }
    </style>

    @php
        // الاسم الكامل: نعطي الأولوية لحقول teacher ثم user->name ثم بريد
        $fullName = trim(collect([
            optional($user->teacher)->first_name.' '.optional($user->teacher)->last_name,
            $user->name,
            ($user->email ? explode('@',$user->email)[0] : null),
            'معلّم'
        ])->first(fn($v) => $v && trim($v) !== ''));

        // الأحرف الأولى للافاتار فقط (نُظهر الاسم الكامل في النص)
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
                                <i class="bi bi-shield-check"></i>
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

        <div class="row g-4">
            {{-- معلوماتي --}}
            <div class="col-lg-5">
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
                        @if(optional($user->teacher)->phone)
                            <div class="mb-3">
                                <div class="text-muted small">الهاتف</div>
                                <div class="fw-semibold">{{ $user->teacher->phone }}</div>
                            </div>
                        @endif
                        @if(optional($user->teacher)->bio)
                            <div class="mb-0">
                                <div class="text-muted small">نبذة</div>
                                <div class="fw-semibold">{{ $user->teacher->bio }}</div>
                            </div>
                        @endif
                        {{-- لا يوجد زر تعديل وفق طلبك --}}
                    </div>
                </div>
            </div>

            {{-- المعهد الحالي وروابط سريعة --}}
            <div class="col-lg-7">
                <div class="card profile-card shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-semibold">
                            <i class="bi bi-building"></i> المعهد الحالي
                        </h6>
                        @if(($institutes ?? collect())->count() > 1)
                            <small class="text-muted">يمكنك التبديل بين معاهدك</small>
                        @endif
                    </div>

                    <div class="card-body">
                        @if($inst)
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
                                        <a href="{{ route('teacher.schedule.index') }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-calendar-week"></i> جدولي
                                        </a>
                                        <a href="{{ route('teacher.classes.index') }}" class="btn btn-sm btn-outline-secondary">
                                            حلقاتي
                                        </a>
                                        <a href="{{ route('teacher.students.index') }}" class="btn btn-sm btn-outline-secondary">
                                            جميع الطلاب
                                        </a>
                                        <a href="{{ route('teacher.library.index') }}" class="btn btn-sm btn-outline-secondary">
                                            المكتبة
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-muted">لا يوجد معهد مرتبط بحسابك حالياً.</div>
                        @endif
                    </div>

                    @if(($institutes ?? collect())->count() > 1)
                        <div class="card-footer bg-white">
                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                @foreach($institutes as $i)
                                    <a href="{{ route('teacher.profile.show', ['institute_id'=>$i->id]) }}"
                                       class="institute-pill {{ $inst && $i->id === $inst->id ? 'active' : '' }}"
                                       title="التبديل إلى: {{ $i->name }}">
                                        <i class="bi bi-arrow-left-right"></i> {{ $i->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection
