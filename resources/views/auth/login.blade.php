<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام إدارة المعاهد الشرعية</title>
    <style>
        /* التنسيقات العامة */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #224b89 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* قسم الصورة */
        .image-section {
            flex: 1;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1589998059171-988d887df646?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .image-content {
            position: relative;
            z-index: 1;
        }

        .image-section h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .image-section p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .features {
            list-style: none;
            margin-top: 30px;
        }

        .features li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .features li i {
            margin-left: 10px;
            color: #1b4cb1;
            font-size: 20px;
        }

        /* قسم النموذج */
        .form-section {
            flex: 1;
            padding: 40px;
            background: #fff;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h2 {
            color: #1b4cb1;
            font-size: 24px;
            font-weight: 700;
        }

        .logo p {
            color: #757575;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #424242;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #1b4cb1;
            outline: none;
            box-shadow: 0 0 0 2px rgba(46, 125, 50, 0.2);
        }

        .error {
            color: #f44336;
            font-size: 14px;
            margin-top: 5px;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .remember-forgot label {
            display: flex;
            align-items: center;
            color: #616161;
            cursor: pointer;
        }

        .remember-forgot input {
            margin-left: 8px;
        }

        .remember-forgot a {
            color: #1b4cb1;
            text-decoration: none;
            transition: color 0.3s;
        }

        .remember-forgot a:hover {
            color: #1b4cb1;
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #1b4cb1;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-submit:hover {
            background-color: #1b4cb1;
        }



        .register-link a:hover {
            text-decoration: underline;
        }

        /* التصميم المتجاوب */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                max-width: 100%;
            }

            .image-section {
                display: none;
            }

            .form-section {
                padding: 30px 20px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container">
    <div class="image-section">
        <div class="image-content">
            <h1>نظام إدارة المعاهد الشرعية</h1>
            <p>منصة متكاملة لإدارة المعاهد الشرعية وتسهيل العمليات التعليمية والإدارية</p>
            <ul class="features">
                <li><i class="fas fa-check-circle"></i> إدارة الطلاب والمناهج الدراسية</li>
                <li><i class="fas fa-check-circle"></i> متابعة الحضور والغياب</li>
                <li><i class="fas fa-check-circle"></i> إحصائيات مفصلة</li>
                <li><i class="fas fa-check-circle"></i> تواصل فعال بين المعلمين والطلاب</li>
            </ul>
        </div>
    </div>

    <div class="form-section">
        <div class="logo">
            <h2>نظام إدارة المعاهد الشرعية</h2>
            <p>سجل الدخول للوصول إلى حسابك</p>
        </div>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="أدخل بريدك الإلكتروني">
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input id="password" type="password" name="password" required placeholder="أدخل كلمة المرور">
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn-submit">تسجيل الدخول</button>
        </form>


    </div>
</div>
</body>
</html>
