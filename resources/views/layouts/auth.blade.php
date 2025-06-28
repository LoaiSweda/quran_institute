<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>@yield('title','تسجيل الدخول')</title>
    <style>
        /* Reset بسيط */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: #ececec;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            color: #2c2c2c;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
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
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-card">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
