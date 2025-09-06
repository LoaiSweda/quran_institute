@extends('layouts.app')

@section('title', 'لوحة تحكم المالك - المعاهد')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header bg-white p-4 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 text-primary">المعاهد المنشأة</h2>
                            <p class="text-muted mb-0"> جميع المعاهد في النظام</p>
                        </div>
                        <span class="badge bg-primary rounded-pill">إجمالي المعاهد: {{ $institutes->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach($institutes as $institute)
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
                    <div class="card institute-card h-100 border-0 shadow-lg">
                        <div class="card-header position-relative p-0 overflow-hidden">
                            @if($institute->image)
                                <img src="{{ asset('storage/' . $institute->image) }}">
                            @else
                                <div class="institute-image-placeholder">
                                    <i class="fas fa-university"></i>
                                </div>
                            @endif
                            <div class="overlay"></div>

                            <h5 class="institute-name text-black">{{ $institute->name }}</h5>
                        </div>

                        <div class="card-body">
                            <div class="institute-info mb-3">
                                <div class="info-item d-flex align-items-center mb-2">
                                    <div class="info-icon bg-light-primary rounded-circle p-2 me-2">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <small class="text-muted">العنوان</small>
                                        <p class="mb-0">{{ $institute->address ?? 'لا يوجد عنوان' }}</p>
                                    </div>
                                </div>

                                <div class="info-item d-flex align-items-center mb-2">
                                    <div class="info-icon bg-light-primary rounded-circle p-2 me-2">
                                        <i class="fas fa-user-tie text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <small class="text-muted">مدير المعهد</small>
                                        <p class="mb-0">
                                            @if($institute->manager && $institute->manager->admin)
                                                {{ $institute->manager->admin->first_name }} {{ $institute->manager->admin->last_name }}
                                            @else
                                                غير معين
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="stats-grid mb-4">
                                <div class="stat-card bg-light-primary">
                                    <div class="stat-icon">
                                        <i class="fas fa-user-graduate text-primary"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4 class="stat-value">{{ $institute->students_count }}</h4>
                                        <span class="stat-label">الطلاب</span>
                                    </div>
                                </div>

                                <div class="stat-card bg-light-info">
                                    <div class="stat-icon">
                                        <i class="fas fa-chalkboard-teacher text-info"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4 class="stat-value">{{ $institute->teachers_count }}</h4>
                                        <span class="stat-label">المعلمين</span>
                                    </div>
                                </div>

                                <div class="stat-card bg-light-success">
                                    <div class="stat-icon">
                                        <i class="fas fa-book text-success"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4 class="stat-value">{{ $institute->subjects_count }}</h4>
                                        <span class="stat-label">المواد</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent border-top-0 pt-0">
                            <a href="{{ route('super-admin.institutes.Details', $institute) }}" class="btn btn-primary w-100 rounded-pill">
                                <i class="fas fa-eye me-2"></i> عرض التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($institutes->count() == 0)
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <div class="empty-icon mb-4">
                                <i class="fas fa-university fa-4x text-muted"></i>
                            </div>
                            <h4 class="text-muted">لا توجد معاهد مسجلة</h4>
                            <p class="text-muted mb-4">لم يتم إنشاء أي معهد حتى الآن في النظام</p>
                            <a href="{{ route('super-admin.institutes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> إنشاء معهد جديد
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('styles')
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #6f42c1;
            --success: #1cc88a;
            --info: #36b9cc;
            --light-bg: #f8f9fc;
            --light-primary: #e8f1ff;
            --light-info: #e8f7fa;
            --light-success: #e6faf3;
        }

        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-header {
            border-left: 4px solid var(--primary);
        }

        .institute-card {
            border-radius: 1rem;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .institute-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1) !important;
        }

        .card-header {
            position: relative;
            padding: 0;
            border-bottom: none;
            height: 180px;
        }

        .institute-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .institute-image-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3.5rem;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
        }

        .institute-name {
            position: absolute;
            bottom: 20px;
            left: 20px;
            font-weight: 700;
            font-size: 1.4rem;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .institute-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1;
        }

        .institute-badge .badge {
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .card-body {
            padding: 1.5rem;
        }

        .info-item {
            padding: 0.5rem 0;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-content small {
            font-size: 0.75rem;
        }

        .info-content p {
            margin: 0;
            font-weight: 500;
            color: #4a4a4a;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
        }

        .stat-card {
            border-radius: 0.75rem;
            padding: 1rem;
            text-align: center;
            transition: transform 0.2s;
            border: 1px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-size: 1.1rem;
        }

        .stat-content h4 {
            margin: 0;
            font-weight: 700;
            color: #4a4a4a;
            font-size: 1.25rem;
        }

        .stat-content .stat-label {
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 500;
        }

        .bg-light-primary {
            background-color: var(--light-primary);
            border-color: rgba(78, 115, 223, 0.2);
        }

        .bg-light-info {
            background-color: var(--light-info);
            border-color: rgba(54, 185, 204, 0.2);
        }

        .bg-light-success {
            background-color: var(--light-success);
            border-color: rgba(28, 200, 138, 0.2);
        }

        .card-footer {
            padding: 0 1.5rem 1.5rem;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(78, 115, 223, 0.3);
        }

        .btn-primary:hover {
            background: #3a5cce;
            box-shadow: 0 6px 8px rgba(78, 115, 223, 0.4);
            transform: translateY(-1px);
        }

        .empty-icon {
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .institute-name {
                font-size: 1.2rem;
            }

            .page-header {
                text-align: center;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // إضافة تأثيرات للبطاقات عند التمرير
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.institute-card').forEach(card => {
                card.style.opacity = 0;
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(card);
            });
        });
    </script>
@endsection
