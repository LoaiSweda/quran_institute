@extends('layouts.app')
@section('title','بروفايل المعلّم')

@section('content')
    <style>
        :root{
              /* تدرّج أزرق-فيروزي حسب رغبتك للمدير */
            --c1:#4f46e5; /* indigo */
            --c2:#06b6d4; /* cyan   */
            --ink:#0f172a; --muted:#6b7280; --ring:#93c5fd;
        }
        .profile-hero{
            position:relative; border-radius:1.25rem; padding:2.25rem; color:#fff;
            background: radial-gradient(1200px 600px at 120% -10%, rgba(255,255,255,.18), transparent 40%),
                        linear-gradient(135deg, var(--c1), var(--c2));
            overflow:hidden; box-shadow:0 10px 30px rgba(2,6,23,.15);
            isolation:isolate;
        }
        .profile-hero:before{
            content:""; position:absolute; inset:-40%; background:
            radial-gradient(400px 180px at 80% 10%, rgba(255,255,255,.14) 0 60%,transparent 70%),
            radial-gradient(300px 160px at 0% 100%, rgba(255,255,255,.08) 0 60%,transparent 70%);
            filter: blur(8px); z-index:-1;
        }
        .avatar{
            width:88px;height:88px;border-radius:50%;
            display:inline-flex;align-items:center;justify-content:center;
            font-weight:800;font-size:30px;letter-spacing:.5px;
            background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.35);
            box-shadow: inset 0 0 0 4px rgba(255,255,255,.08);
            backdrop-filter: blur(6px);
        }
        .chip{
            display:inline-flex;align-items:center;gap:.35rem;
            padding:.35rem .7rem;border-radius:999px;
            background:rgba(255,255,255,.16); border:1px solid rgba(255,255,255,.28);
            font-weight:600; font-size:.85rem;
        }
        .hero-meta{opacity:.95}
        .hero-meta .item{display:flex;align-items:center;gap:.5rem;margin-inline-start:1rem}
        .hero-meta i{opacity:.9}

        /* Cards */
        .card-clean{border:0;border-radius:1rem;box-shadow:0 8px 24px rgba(2,6,23,.06)}
        .card-clean .card-header{background:#fff;border-bottom:1px solid #eef2f7;border-radius:1rem 1rem 0 0}
        .section-title{margin:0;font-weight:700;color:var(--ink)}
        .kv{display:grid;grid-template-columns:160px 1fr;gap:.5rem .75rem;align-items:center;margin-bottom:.9rem}
        .kv .k{color:var(--muted);font-size:.9rem}
        .kv .v{color:#111827;font-weight:600}
        .muted{color:var(--muted)}
        .divider{height:1px;background:#f1f5f9;margin:1rem 0}

        /* Institute */
        .inst-box{display:flex;gap:1rem;align-items:center}
        .inst-logo{width:76px;height:76px;border-radius:.75rem;overflow:hidden;flex:0 0 76px;background:#f8fafc;display:flex;align-items:center;justify-content:center}
        .inst-actions .btn{--bs-btn-font-weight:600}
        .pill{
            display:inline-flex;align-items:center;gap:.5rem;padding:.35rem .8rem;border-radius:999px;
            border:1px solid #e5e7eb;background:#fff;transition:.25s;
        }
        .pill:hover{border-color:#c7d2fe;box-shadow:0 0 0 3px rgba(147,197,253,.35)}
        .pill.active{background:rgba(59,130,246,.06);border-color:#60a5fa}
        .inst-pills{display:flex;flex-wrap:wrap;gap:.5rem;justify-content:end}

        /* Buttons look */
        .btn-primary{box-shadow:0 6px 18px rgba(37,99,235,.18)}
        .btn-outline-secondary:hover{background:#f8fafc}

        /* Responsive tweaks */
        @media (max-width: 992px){
            .kv{grid-template-columns:120px 1fr}
        }
        @media (max-width: 576px){
            .hero-meta{display:grid;gap:.35rem}
            .kv{grid-template-columns:1fr}
        }
    </style>

    @php
        // الاسم الكامل (بدون أي قيم ثابتة)
        $fullName = trim(collect([
            optional($user->teacher)->first_name.' '.optional($user->teacher)->last_name,
            $user->name,
            ($user->email ? explode('@',$user->email)[0] : null),
        ])->first(fn($v) => $v && trim($v) !== ''));

        // الأحرف الأولى للأفاتار
        $baseForInitials = $fullName ?: ($user->email ? explode('@',$user->email)[0] : 'U');
        $parts = preg_split('/\s+/u', trim($baseForInitials));
        $initials = mb_strtoupper(mb_substr($parts[0] ?? 'U',0,1).mb_substr($parts[1] ?? '',0,1));
    @endphp

    <div class="container-fluid">

        {{-- HERO --}}
        <div class="profile-hero mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar">{{ $initials }}</div>
                    <div class="text-end">
                        <div class="fs-3 fw-bold">{{ $fullName }}</div>
                        <div class="mt-1">
                            <span class="chip">
                                <i class="bi bi-shield-check"></i>
                                {{ $user->role->name ?? '—' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="hero-meta d-flex align-items-center">
                    @if($user->email)
                        <div class="item"><i class="bi bi-envelope"></i><span>{{ $user->email }}</span></div>
                    @endif
                    @if(optional($user->teacher)->phone)
                        <div class="item"><i class="bi bi-telephone"></i><span>{{ $user->teacher->phone }}</span></div>
                    @endif
                    <div class="item"><i class="bi bi-calendar2-check"></i>
                        <span>انضمّ {{ optional($user->created_at)->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- بياناتي --}}
            <div class="col-lg-5">
                <div class="card card-clean h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="section-title"><i class="bi bi-person-circle"></i> بياناتي</h6>
                    </div>
                    <div class="card-body text-end">
                        <div class="kv">
                            <div class="k">الاسم الكامل</div>
                            <div class="v">{{ $fullName }}</div>
                        </div>
                        <div class="kv">
                            <div class="k">البريد الإلكتروني</div>
                            <div class="v">{{ $user->email }}</div>
                        </div>
                        @if(optional($user->teacher)->phone)
                            <div class="kv">
                                <div class="k">الهاتف</div>
                                <div class="v">{{ $user->teacher->phone }}</div>
                            </div>
                        @endif
                        @if(optional($user->teacher)->bio)
                            <div class="divider"></div>
                            <div class="muted mb-1">نبذة</div>
                            <div class="fw-semibold">{{ $user->teacher->bio }}</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- المعهد الحالي + روابط --}}
            <div class="col-lg-7">
                <div class="card card-clean h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="section-title"><i class="bi bi-building"></i> المعهد الحالي</h6>
                        @if(($institutes ?? collect())->count() > 1)
                            <small class="muted">يمكنك التبديل بين معاهدك</small>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($inst)
                            <div class="inst-box">
                                <div class="inst-logo">
                                    @if($inst->image)
                                        <img src="{{ asset('storage/'.$inst->image) }}" alt="Institute" style="width:100%;height:100%;object-fit:cover">
                                    @else
                                        <i class="bi bi-building fs-2 text-secondary"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1 text-end">
                                    <div class="fw-bold fs-5">{{ $inst->name }}</div>
                                    <div class="muted small"><i class="bi bi-geo-alt"></i> {{ $inst->address ?? '—' }}</div>

                                    <div class="inst-actions mt-3 d-flex flex-wrap gap-2 justify-content-end">
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
                            <div class="muted">لا يوجد معهد مرتبط بحسابك حالياً.</div>
                        @endif
                    </div>

                    @if(($institutes ?? collect())->count() > 1)
                        <div class="card-footer bg-white">
                            <div class="inst-pills">
                                @foreach($institutes as $i)
                                    <a class="pill {{ $inst && $i->id === $inst->id ? 'active' : '' }}"
                                       href="{{ route('teacher.profile.show', ['institute_id'=>$i->id]) }}"
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
