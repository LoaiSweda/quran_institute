<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المالك - المعاهد</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4a6cf7;
            --primary-dark: #3a56d4;
            --secondary: #6c757d;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #198754;
            --info: #0dcaf0;
            --warning: #ffc107;
            --danger: #dc3545;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-800: #343a40;
        }

        /* أنماط التخطيط الأساسي */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Tajawal', sans-serif;
            display: flex;
            min-height: 100vh;
            background: #f5f7fb;
            color: var(--dark);
        }

        html, body {
            overflow-x: hidden;
        }

        /* ===== Sidebar ===== */
        .sidebar {
            width: 240px;
            background: #2c3e50;
            color: #ecf0f1;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; bottom: 0; right: 0; /* RTL */
        }
        .sidebar .logo {
            padding: 20px;
            font-size: 1.4rem;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar nav {
            flex: 1;
            overflow-y: auto;
        }
        .sidebar ul {
            list-style: none;
            margin: 0; padding: 0;
        }
        .sidebar li + li {
            margin-top: 4px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #bdc3c7;
            text-decoration: none;
            transition: background .2s, color .2s;
        }
        .sidebar a:hover,
        .sidebar .active > a {
            background: #34495e;
            color: #ecf0f1;
        }
        .sidebar a i {
            margin-left: 10px; /* أيقونة ناحية اليسار بصيغة RTL */
            font-size: 1.1rem;
        }

        /* ===== Main content adjustment ===== */
        .main {
            margin-right: 260px;
            margin-left: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* ===== Navbar ===== */
        .app-navbar {
            height: 60px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: fixed;
            top: 0;
            right: 240px;
            left: 0;
            z-index: 1000;
        }

        .navbar-left {
            display: flex;
            align-items: center;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #333;
            margin-left: 16px;
        }

        .app-title {
            font-size: 1.2rem;
            color: #2c3e50;
        }

        .navbar-right {
            display: flex;
            align-items: center;
        }

        .user-welcome {
            font-size: 0.95rem;
            color: #2c3e50;
            margin-left: 24px;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            background: #c0392b;
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background .2s;
        }

        .btn-logout:hover {
            background: #a93226;
        }

        /* لضمان أن المحتوى لا يختفي خلف الـ navbar */
        .main .content {
            padding-top: 80px; /* ارتفاع navbar + مسافة */
            padding-bottom: 20px;
        }

        /* أنماط صفحة المعاهد */
        .page-header {
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-right: 4px solid var(--primary);
        }

        .institute-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .institute-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            position: relative;
            padding: 0;
            border-bottom: none;
            height: 160px;
            overflow: hidden;
        }

        .card-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .institute-card:hover .card-header img {
            transform: scale(1.05);
        }

        .institute-image-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50%;
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%);
        }

        .institute-name {
            position: absolute;
            bottom: 15px;
            right: 20px;
            font-weight: 700;
            font-size: 1.3rem;
            margin: 0;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .card-body {
            padding: 1.25rem;
        }

        .info-item {
            margin-bottom: 0.75rem;
        }

        .info-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(74, 108, 247, 0.1);
            border-radius: 8px;
            color: var(--primary);
            flex-shrink: 0;
            margin-left: 0.75rem;
        }

        .info-content small {
            font-size: 0.75rem;
            color: var(--secondary);
        }

        .info-content p {
            margin: 0;
            font-weight: 500;
            color: var(--dark);
            font-size: 0.9rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            margin: 1.25rem 0;
        }

        .stat-card {
            border-radius: 8px;
            padding: 0.75rem;
            text-align: center;
            background-color: var(--gray-100);
        }

        .stat-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.25rem;
            font-size: 0.9rem;
        }

        .stat-students .stat-icon {
            background-color: rgba(25, 135, 84, 0.15);
            color: var(--success);
        }

        .stat-teachers .stat-icon {
            background-color: rgba(13, 202, 240, 0.15);
            color: var(--info);
        }

        .stat-subjects .stat-icon {
            background-color: rgba(255, 193, 7, 0.15);
            color: var(--warning);
        }

        .stat-content h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .stat-content .stat-label {
            font-size: 0.7rem;
            color: var(--secondary);
            font-weight: 500;
        }

        .card-footer {
            padding: 0 1.25rem 1.25rem;
            background: transparent;
            border-top: 0;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
        }

        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .empty-icon {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            
            .stat-card {
                display: flex;
                align-items: center;
                justify-content: space-between;
                text-align: right;
                padding: 0.75rem 1rem;
            }
            
            .stat-content {
                display: flex;
                align-items: center;
            }
            
            .stat-content h4 {
                margin-left: 0.5rem;
            }
            
            /* تكيف الشريط الجانبي والشريط العلوي للشاشات الصغيرة */
            .sidebar {
                width: 70px;
                z-index: 1001;
            }
            
            .sidebar .logo span,
            .sidebar a span {
                display: none;
            }
            
            .sidebar a i {
                margin-left: 0;
                font-size: 1.3rem;
            }
            
            .main {
                margin-right: 90px;
            }
            
            .app-navbar {
                right: 70px;
            }
        }
    </style>
</head>
<body>
    @include('partials.sidebar')


    <div class="main">
        <!-- الشريط العلوي -->
        @include('partials.navbar')

        <!-- محتوى الصفحة -->
        <div class="content">
            <div class="container-fluid py-4">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="page-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-1 text-primary">المعاهد المنشأة</h2>
                                    <p class="text-muted mb-0">جميع المعاهد في النظام</p>
                                </div>
                                <span class="badge bg-primary rounded-pill fs-6">إجمالي المعاهد: {{ $institutes->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @foreach($institutes as $institute)
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
                            <div class="card institute-card h-100">
                                <div class="card-header">
                                    @if($institute->image)
                                        <img src="{{ asset('storage/' . $institute->image) }}" alt="{{ $institute->name }}">
                                    @else
                                        <div class="institute-image-placeholder">
                                            <i class="fas fa-university"></i>
                                        </div>
                                    @endif
                                    <div class="overlay"></div>
                                    <h5 class="institute-name">{{ $institute->name }}</h5>
                                </div>

                                <div class="card-body">
                                    <div class="institute-info">
                                        <div class="info-item d-flex align-items-center">
                                            <div class="info-icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                            <div class="info-content">
                                                <small>العنوان</small>
                                                <p>{{ $institute->address ?? 'لا يوجد عنوان' }}</p>
                                            </div>
                                        </div>

                                        <div class="info-item d-flex align-items-center">
                                            <div class="info-icon">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                            <div class="info-content">
                                                <small>مدير المعهد</small>
                                                <p>
                                                    @if($institute->manager && $institute->manager->admin)
                                                        {{ $institute->manager->admin->first_name }} {{ $institute->manager->admin->last_name }}
                                                    @else
                                                        غير معين
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="stats-grid">
                                        <div class="stat-card stat-students">
                                            <div class="stat-icon">
                                                <i class="fas fa-user-graduate"></i>
                                            </div>
                                            <div class="stat-content">
                                                <h4>{{ $institute->students_count }}</h4>
                                                <span class="stat-label">الطلاب</span>
                                            </div>
                                        </div>

                                        <div class="stat-card stat-teachers">
                                            <div class="stat-icon">
                                                <i class="fas fa-chalkboard-teacher"></i>
                                            </div>
                                            <div class="stat-content">
                                                <h4>{{ $institute->teachers_count }}</h4>
                                                <span class="stat-label">المعلمين</span>
                                            </div>
                                        </div>

                                        <div class="stat-card stat-subjects">
                                            <div class="stat-icon">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <div class="stat-content">
                                                <h4>{{ $institute->subjects_count }}</h4>
                                                <span class="stat-label">المواد</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <a href="{{ route('super-admin.institutes.Details', $institute) }}" class="btn btn-primary w-100">
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
                            <div class="empty-state">
                                <div class="empty-icon mb-4">
                                    <i class="fas fa-university"></i>
                                </div>
                                <h4 class="text-muted">لا توجد معاهد مسجلة</h4>
                                <p class="text-muted mb-4">لم يتم إنشاء أي معهد حتى الآن في النظام</p>
                                <a href="{{ route('super-admin.institutes.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i> إنشاء معهد جديد
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأثيرات الظهور للبطاقات
            const cards = document.querySelectorAll('.institute-card');
            
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });

            // تبديل الشريط الجانبي في الشاشات الصغيرة
            const toggleSidebar = document.querySelector('.toggle-sidebar');
            const sidebar = document.querySelector('.sidebar');
            
            toggleSidebar.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                
                if (sidebar.classList.contains('collapsed')) {
                    document.querySelector('.main').style.marginRight = '90px';
                    document.querySelector('.app-navbar').style.right = '70px';
                } else {
                    document.querySelector('.main').style.marginRight = '260px';
                    document.querySelector('.app-navbar').style.right = '240px';
                }
            });
        });
    </script>
</body>
</html>