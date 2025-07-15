<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>@yield('title','لوحة التحكم')</title>
    <style>
         /* Reset بسيط */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            display: flex;
            min-height: 100vh;
            background: #f0f2f5;
        }

        html, body {
            overflow-x: hidden;
        }

        .auth-card {
            background: #ffffff;
            width: 100%;
            max-width: 380px;
            padding: 30px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .auth-card h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.6rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.95rem;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: border-color .2s;
        }
        input:focus {
            border-color: #888;
            outline: none;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .remember-forgot a {
            text-decoration: none;
            color: #555;
        }
        .remember-forgot a:hover {
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #4a4a4a;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background .2s;
        }
        .btn-submit:hover {
            background: #3a3a3a;
        }

        .error {
            color: #d22828;
            font-size: 0.85rem;
            margin-top: 4px;
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
        }

    </style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    @stack('styles')
</head>
<body>

    {{-- Sidebar عام --}}
    @include('partials.sidebar')

    <div class="main">
        {{-- Navbar عام --}}
        @include('partials.navbar')

        {{-- المحتوى الخاص بكل صفحة --}}
        <div class="content">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

</body>
</html>
