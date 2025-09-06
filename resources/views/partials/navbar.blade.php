<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة المعاهد الشرعية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2c5c7f;
            --primary-dark: #1e415c;
            --primary-light: #3a7fb8;
            --secondary-color: #4a8c68;
            --accent-color: #d4af37;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --sidebar-width: 250px;
            --header-height: 70px;
            --sidebar-collapsed-width: 70px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7f9;
            color: #333;
            direction: rtl;
            overflow-x: hidden;
        }
        
        .app-navbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            height: var(--header-height);
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .navbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .toggle-sidebar {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }
        
        .toggle-sidebar:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .app-title {
            color: #b3b3b4ff;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .app-title::before {
            content: "\f19d";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            color: var(--accent-color);
        }
        
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-welcome {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            font-size: 0.95rem;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .btn-logout {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .btn-logout:hover {
            background-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }
        
        /* Sidebar Styles - تم التعديل ليتناسب مع النافبار */
        .sidebar {
            position: fixed;
            top: var(--header-height);
            right: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--header-height));
            background: linear-gradient(to bottom, var(--primary-dark), var(--primary-color));
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 999;
            padding: 20px 0;
        }
        
        .sidebar.collapsed {
            transform: translateX(var(--sidebar-width));
        }
        
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        
        .sidebar li {
            margin-bottom: 5px;
        }
        
        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s;
            gap: 10px;
            border-right: 3px solid transparent;
        }
        
        /* لا تجعل الـhover بنفس شكل المحدد */
        .sidebar a:hover {
        background-color: transparent;       /* بدون تظليل خلفية */
        color: #fff;                          /* مجرّد تفتيح النص */
        border-right-color: var(--accent-color);
        }

        /* المظهر الوحيد الذي يظهر كتحديد حقيقي */
        .sidebar a.active {
        background-color: rgba(255, 255, 255, 0.18);
        color: #fff;
        border-right-color: var(--accent-color);
        font-weight: 600;
        }

        /* إزالة هالة التركيز الزرقاء حتى لا تبدو كأنها تحديد ثانٍ */
        .sidebar a:focus {
        outline: none;
        box-shadow: none;
        }

        .sidebar {
        overflow-y: auto;           /* التمرير موجود لكن الشريط مخفي */
        -ms-overflow-style: none;   /* IE/Edge قديم */
        scrollbar-width: none;      /* Firefox */
        }
        .sidebar::-webkit-scrollbar { /* Chrome/Edge/Safari */
        width: 0;
        height: 0;
        }

        
        .sidebar a i {
            width: 24px;
            text-align: center;
            color: var(--accent-color);
        }
        
        /* Main Content */
        .main-content {
            margin-top: var(--header-height);
            margin-right: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s ease;
            min-height: calc(100vh - var(--header-height));
        }
        
        .main-content.expanded {
            margin-right: 0;
        }
        
        .content-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }
        
        h2 {
            color: var(--primary-color);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        p {
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .app-title {
                font-size: 1.2rem;
            }
            
            .user-welcome {
                display: none;
            }
            
            .btn-logout span {
                display: none;
            }
            
            .btn-logout {
                padding: 10px;
            }
            
            .sidebar {
                width: var(--sidebar-collapsed-width);
            }
            
            .sidebar a span {
                display: none;
            }
            
            .sidebar a {
                justify-content: center;
                padding: 15px 10px;
            }
            
            .main-content {
                margin-right: var(--sidebar-collapsed-width);
            }
        }
    </style>
</head>
<body>
    <header class="app-navbar">
        <div class="navbar-left">
            <h1 class="app-title">نظام إدارة المعاهد الشرعية</h1>
        </div>

        @php
            $user = auth()->user();
            $role = $user->role?->name;
            if ($role === 'teacher') {
                $displayName = $user->teacher?->first_name;
            } else {
                $displayName = $user->admin?->first_name ?? $user->email;
            }
        @endphp
        
        <div class="navbar-right">
            <div class="user-welcome">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    مرحباً، <strong>أستاذ {{ $displayName }}</strong>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </div>
    </header>



    <script>
        const toggleSidebar = document.querySelector('.toggle-sidebar');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        toggleSidebar.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        function handleResize() {
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                toggleSidebar.querySelector('i').classList.remove('fa-bars');
                toggleSidebar.querySelector('i').classList.add('fa-times');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
                toggleSidebar.querySelector('i').classList.remove('fa-times');
                toggleSidebar.querySelector('i').classList.add('fa-bars');
            }
        }
        
        window.addEventListener('resize', handleResize);
        
        document.addEventListener('DOMContentLoaded', function() {
            handleResize();
        });
    </script>

    <script>
  function wireSidebarActive() {
    const links = document.querySelectorAll('.sidebar a');

    function setActive(el) {
      links.forEach(l => l.classList.remove('active'));
      el.classList.add('active');
    }

    const saved = localStorage.getItem('sidebarActiveHref');
    let initial =
      (saved && document.querySelector(`.sidebar a[href="${saved}"]`)) ||
      document.querySelector(`.sidebar a[href="${location.pathname}"]`) ||
      links[0];

    if (initial) setActive(initial);

    // عند الضغط: فعّل واحد فقط واحفظ الاختيار
    links.forEach(link => {
      link.addEventListener('click', e => {
        // إذا كان الرابط لا يغيّر الصفحة (spa/#) امنع التنقّل وأظهر التحديد
        const href = link.getAttribute('href') || '';
        if (href.startsWith('#')) {
          e.preventDefault();
          setActive(link);
        }
        localStorage.setItem('sidebarActiveHref', href);
      });
    });
  }

  document.addEventListener('DOMContentLoaded', wireSidebarActive);
</script>

</body>
</html>

