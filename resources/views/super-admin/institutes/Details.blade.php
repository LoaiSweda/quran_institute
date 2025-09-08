@extends('layouts.app')

@section('title', 'تفاصيل المعهد - ' . $institute->name)

@section('content')
    <div class="container-fluid py-4">
        <!-- رأس الصفحة -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header bg-gradient-primary p-4 rounded shadow-sm text-black">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1"><i class="fas fa-university me-2"></i>{{ $institute->name }}</h2>
                            <p class="mb-0 opacity-8">نظرة عامة شاملة على إحصائيات وأداء المعهد</p>
                        </div>
                        <a href="{{ route('super-admin.institutes') }}" class="btn btn-light">
                            <i class="fas fa-arrow-right me-2"></i> العودة إلى القائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- البطاقات الإحصائية الكبيرة -->
        <div class="row">
            <!-- الطلاب -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card stat-card h-100 border-0 shadow-lg hover-card">
                    <a href="{{ route('super-admin.institutes.students', $institute) }}" class="card-link text-decoration-none">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-user-graduate fa-2x text-primary"></i>
                            </div>
                            <h3 class="stat-value text-primary mb-1">{{ $institute->students_count }}</h3>
                            <p class="stat-label text-muted mb-0">الطلاب</p>
                            <div class="stat-details mt-3">
                                <div class="progress mb-2" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $stats['attendance_rate'] }}%"></div>
                                </div>
                                <small class="text-muted">نسبة الحضور: {{ $stats['attendance_rate'] }}%</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- المعلمين -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card stat-card h-100 border-0 shadow-lg hover-card">
                    <a href="{{ route('super-admin.institutes.teachers', $institute) }}" class="card-link text-decoration-none">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-chalkboard-teacher fa-2x text-info"></i>
                            </div>
                            <h3 class="stat-value text-info mb-1">{{ $institute->teachers_count }}</h3>
                            <p class="stat-label text-muted mb-0">المعلمين</p>
                            <div class="stat-details mt-3">
                                <div class="d-flex justify-content-center align-items-center">
                                    <span class="badge bg-success me-2">
                                        <i class="fas fa-check-circle me-1"></i> {{ $stats['active_teachers'] }} معلمين
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- الفصول -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card stat-card h-100 border-0 shadow-lg hover-card">
                    <a href="{{ route('super-admin.institutes.classes', $institute) }}" class="card-link text-decoration-none">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-door-open fa-2x text-success"></i>
                            </div>
                            <h3 class="stat-value text-success mb-1">{{ $institute->classes_count }}</h3>
                            <p class="stat-label text-muted mb-0">الفصول</p>
                            <div class="stat-details mt-3">
                                <span class="badge bg-primary">
                                    <i class="fas fa-clock me-1"></i> {{ $stats['total_sessions'] }} جلسة
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- المواد -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card stat-card h-100 border-0 shadow-lg hover-card">
                    <a href="{{ route('super-admin.institutes.subjects', $institute) }}" class="card-link text-decoration-none">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-book fa-2x text-warning"></i>
                            </div>
                            <h3 class="stat-value text-warning mb-1">{{ $institute->subjects_count }}</h3>
                            <p class="stat-label text-muted mb-0">المواد</p>
                            <div class="stat-details mt-3">
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i> {{ $stats['active_subjects'] }} نشطة
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- التسميعات -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card stat-card h-100 border-0 shadow-lg hover-card">
                    <a href="{{ route('super-admin.institutes.memorizations', $institute) }}" class="card-link text-decoration-none">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-quran fa-2x text-purple"></i>
                            </div>
                            <h3 class="stat-value text-purple mb-1">{{ $stats['total_memorizations'] }}</h3>
                            <p class="stat-label text-muted mb-0">التسميعات</p>
                            <div class="stat-details mt-3">
                                <small class="text-muted">إجمالي جلسات التسميع</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- الامتحانات -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card stat-card h-100 border-0 shadow-lg hover-card">
                    <a href="{{ route('super-admin.institutes.exams', $institute) }}" class="card-link text-decoration-none">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-file-alt fa-2x text-danger"></i>
                            </div>
                            <h3 class="stat-value text-danger mb-1">{{ $stats['total_exams'] }}</h3>
                            <p class="stat-label text-muted mb-0">الامتحانات</p>
                            <div class="stat-details mt-3">
                                <small class="text-muted">إجمالي الاختبارات</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- أولياء الأمور -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card stat-card h-100 border-0 shadow-lg hover-card">
                    <a href="{{ route('super-admin.institutes.guardians', $institute) }}" class="card-link text-decoration-none">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-users fa-2x text-secondary"></i>
                            </div>
                            <h3 class="stat-value text-secondary mb-1">{{ $stats['total_guardians'] }}</h3>
                            <p class="stat-label text-muted mb-0">أولياء الأمور</p>
                            <div class="stat-details mt-3">
                                <small class="text-muted">مرتبطين بالطلاب</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- الجداول -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card stat-card h-100 border-0 shadow-lg hover-card">
                    <a href="{{ route('super-admin.institutes.schedules', $institute) }}" class="card-link text-decoration-none">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-calendar-alt fa-2x text-indigo"></i>
                            </div>
                            <h3 class="stat-value text-indigo mb-1">{{ $stats['total_sessions'] }}</h3>
                            <p class="stat-label text-muted mb-0">الجداول</p>
                            <div class="stat-details mt-3">
                                <small class="text-muted">جلسات أسبوعية</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- معلومات المعهد الأساسية -->
        <div class="row mt-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>معلومات المعهد</h5>
                        <span class="badge bg-primary">ID: {{ $institute->id }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="info-icon rounded-circle p-2 me-3">
                                            <i class="fas fa-signature text-primary"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">اسم المعهد</small>
                                            <strong>{{ $institute->name }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="info-icon rounded-circle p-2 me-3">
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">العنوان</small>
                                            <strong>{{ $institute->address ?? 'غير محدد' }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="info-icon rounded-circle p-2 me-3">
                                            <i class="fas fa-calendar-day text-primary"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">تاريخ الإنشاء</small>
                                            <strong>{{ $institute->created_at->format('Y-m-d') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="info-icon rounded-circle p-2 me-3">
                                            <i class="fas fa-user-tie text-primary"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">مدير المعهد</small>
                                            <strong>
                                                @if($institute->manager && $institute->manager->admin)
                                                    {{ $institute->manager->admin->first_name }} {{ $institute->manager->admin->last_name }}
                                                @else
                                                    غير معين
                                                @endif
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="info-icon rounded-circle p-2 me-3">
                                            <i class="fas fa-envelope text-primary"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">البريد الإلكتروني</small>
                                            <strong>
                                                @if($institute->manager && $institute->manager->admin && $institute->manager->admin->user)
                                                    {{ $institute->manager->admin->user->email }}
                                                @else
                                                    غير متوفر
                                                @endif
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="info-icon rounded-circle p-2 me-3">
                                            <i class="fas fa-phone text-primary"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">الهاتف</small>
                                            <strong>
                                                @if($institute->manager && $institute->manager->admin)
                                                    {{ $institute->manager->admin->phone ?? 'غير متوفر' }}
                                                @else
                                                    غير متوفر
                                                @endif
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>نظرة سريعة</h5>
                    </div>
                    <div class="card-body">
                        <div class="quick-stats">
                            <div class="stat-item d-flex justify-content-between align-items-center mb-3 p-2 rounded bg-light">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-check text-primary me-2"></i>
                                    <span>متوسط نسبة الحضور</span>
                                </div>
                                <span class="badge bg-primary">{{ $stats['attendance_rate'] }}%</span>
                            </div>
                            <div class="stat-item d-flex justify-content-between align-items-center mb-3 p-2 rounded bg-light">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-bolt text-success me-2"></i>
                                    <span>نسبة النشاط</span>
                                </div>
                                <span class="badge bg-success">{{ round(($stats['active_subjects'] / max($institute->subjects_count, 1)) * 100) }}%</span>
                            </div>
                            <div class="stat-item d-flex justify-content-between align-items-center mb-3 p-2 rounded bg-light">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-users text-info me-2"></i>
                                    <span>كثافة الفصول</span>
                                </div>
                                <span class="badge bg-info">{{ round($institute->students_count / max($institute->classes_count, 1)) }} طالب/فصل</span>
                            </div>
                            <div class="stat-item d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-pie text-warning me-2"></i>
                                    <span>نسبة الإشغال</span>
                                </div>
                                <span class="badge bg-warning">
                                    @php
                                        $occupancyRate = min(100, round(($institute->students_count / ($institute->classes_count * 20)) * 100));
                                    @endphp
                                    {{ $occupancyRate }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        :root {
            --purple: #6f42c1;
            --indigo: #6610f2;
        }

        .bg-gradient-primary {
            background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%) !important;
        }

        .stat-card {
            border-radius: 1rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .hover-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.15) !important;
        }

        .card-link { text-decoration: none; color: inherit; }
        .card-link:hover { text-decoration: none; color: inherit; }

        .floating-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            opacity: 0.1;
            font-size: 2.5rem;
        }

        /* === الأيقونات بلا خلفية === */
        .stat-icon{
            width: auto;
            height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* تكبير بسيط عند التحويم */
        .stat-card:hover .stat-icon i { transform: scale(1.06); }
        .stat-icon i { transition: transform .25s ease; }

        .stat-value { font-size: 2.5rem; font-weight: 700; transition: all 0.3s ease; }
        .stat-card:hover .stat-value { transform: scale(1.03); }
        .stat-label { font-size: 1.1rem; font-weight: 500; }

        .stat-details { border-top: 1px solid rgba(0, 0, 0, 0.05); padding-top: 0.8rem; }

        .bg-purple { background-color: var(--purple) !important; }
        .text-purple { color: var(--purple) !important; }
        .bg-indigo { background-color: var(--indigo) !important; }
        .text-indigo { color: var(--indigo) !important; }

        .info-icon {
            width: 40px; height: 40px; display:flex; align-items:center; justify-content:center;
            background: transparent !important; /* بلا خلفية */
        }

        .bg-light-primary { background-color: rgba(94, 114, 228, 0.1) !important; }

        .quick-stats .stat-item { transition: all 0.3s ease; }
        .quick-stats .stat-item:hover {
            background-color: rgba(94, 114, 228, 0.15) !important;
            transform: translateX(5px);
        }

        @media (max-width: 768px) {
            .stat-value { font-size: 2rem; }
            .floating-icon { font-size: 2rem; }
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // دخول ناعم للبطاقات
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

            document.querySelectorAll('.stat-card').forEach(card => {
                card.style.opacity = 0;
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(card);
            });

            // hover
            document.querySelectorAll('.stat-card').forEach(card => {
                card.addEventListener('mouseenter', () => card.style.transform = 'translateY(-8px)');
                card.addEventListener('mouseleave', () => card.style.transform = 'translateY(0)');
            });
        });
    </script>
@endsection
