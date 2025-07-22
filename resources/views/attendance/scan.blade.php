<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>مسح باركود حضور الطلاب</title>
  <script src="https://unpkg.com/@zxing/library@latest"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body { 
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
      text-align: center; 
      padding: 1rem; 
      background-color: #f5f7fa;
      color: #333;
    }
    .container {
      max-width: 800px;
      margin: 0 auto;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      padding: 25px;
    }
    h1 {
      color: #2c3e50;
      margin-bottom: 25px;
      font-size: 28px;
    }
    video { 
      width: 100%; 
      max-width: 500px; 
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      margin: 20px auto;
    }
    #status {
      margin: 20px 0;
      padding: 12px;
      font-size: 18px;
      background: #f8f9fa;
      border-radius: 8px;
      min-height: 28px;
    }
    select {
      padding: 12px 15px;
      font-size: 16px;
      margin-bottom: 20px;
      width: 100%;
      max-width: 500px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background: white;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    #markAbsent {
      background: #e74c3c;
      color: white;
      border: none;
      padding: 14px 28px;
      font-size: 18px;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 20px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(231, 76, 60, 0.2);
    }
    #markAbsent:hover {
      background: #c0392b;
      transform: translateY(-2px);
      box-shadow: 0 6px 8px rgba(231, 76, 60, 0.3);
    }
    #markAbsent:disabled {
      background: #95a5a6;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }
    .info-box {
      background: #e8f4fc;
      border-left: 4px solid #3498db;
      padding: 15px;
      border-radius: 4px;
      margin: 20px 0;
      text-align: right;
    }
    .success { color: #27ae60; }
    .error { color: #e74c3c; }
    .loading {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(255,255,255,.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 1s ease-in-out infinite;
      margin-right: 10px;
    }

    .session-info {
      margin: 15px 0;
      padding: 10px;
      background-color: #f8f9fa;
      border-radius: 8px;
      border-left: 4px solid #3498db;
    }
    
    .session-ended {
      border-left-color: #27ae60;
    }
    
    .session-not-ended {
      border-left-color: #e74c3c;
    }
    
    .time-remaining {
      font-weight: bold;
      font-size: 16px;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>مسح باركود حضور الطلاب</h1>
    
    <div class="info-box">
      <strong>تعليمات:</strong>
      <ol style="text-align: right; margin: 10px 0; padding-right: 20px;">
        <li>اختر جدول الحصة من القائمة</li>
        <li>وجه الكاميرا نحو كود الطالب لتسجيل حضوره</li>
        <li>اضغط على زر تسجيل الغياب عند انتهاء الحصة</li>
      </ol>
    </div>

     <div id="sessionInfo" class="session-info" style="display: none;">
      <div id="sessionStatus"></div>
      <div id="timeRemaining" class="time-remaining"></div>
    </div>
    
    @php
        $days = [
            0 => 'الأحد', 1 => 'الإثنين', 2 => 'الثلاثاء',
            3 => 'الأربعاء',4 => 'الخميس',5 => 'الجمعة',
            6 => 'السبت',
        ];
    @endphp
        
    <label for="schedule">اختر جدول الحصة (يوم {{ $days[\Carbon\Carbon::now('Asia/Damascus')->dayOfWeek] }}):</label>
    <select id="schedule">
      <option value="">-- اختر جدول الحصة --</option>
      @foreach($schedules as $sch)
          <option value="{{ $sch->id }}">
          الصف: {{ $sch->educationClass->name }} | 
          {{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }} - 
          {{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }}
          </option>
      @endforeach
    </select>

    <video id="preview" autoplay muted playsinline></video>
    <div id="status">وجّه الكاميرا إلى باركود الطالب…</div>

    {{-- إضافة زر لتسجيل الغياب --}}
    <button id="markAbsentBtn" class="btn btn-danger mt-3">
        تسجيل الغياب للطلاب المتبقين
    </button>
     
    <div id="attendanceStatus" class="mt-3" style="display: none;">
        <span class="badge bg-success">تم تسجيل الغياب</span>
    </div>  
</div>

<script>
    (async () => {
      const codeReader = new ZXing.BrowserMultiFormatReader();
      const videoElem = document.getElementById('preview');
      const statusElem = document.getElementById('status');
      const markAbsentBtn = document.getElementById('markAbsent');

      // بدء الكاميرا
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
          video: { 
            facingMode: 'environment',
            width: { ideal: 1280 },
            height: { ideal: 720 }
          } 
        });
        videoElem.srcObject = stream;
      } catch (e) {
        statusElem.textContent = '❌ خطأ في الوصول إلى الكاميرا. تأكد من السماح باستخدام الكاميرا';
        statusElem.className = 'error';
        console.error(e);
        return;
      }

      let processing = false;
      codeReader.decodeFromVideoElementContinuously(videoElem, async (result) => {
        if (processing || !result) return;
        processing = true;

        const qr = result.text;
        const scheduleId = document.getElementById('schedule').value;
        if (!scheduleId) {
          statusElem.textContent = '❌ الرجاء اختيار جدول الحصة أولاً';
          statusElem.className = 'error';
          processing = false;
          return;
        }

        statusElem.textContent = `تم المسح: ${qr} — جاريّ التسجيل…`;
        statusElem.className = '';

        try {
          const resp = await fetch('/attendance/scan', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
              qr: qr,
              session_schedule_id: scheduleId
            })
          });
          
          // التحقق مما إذا كان الرد ناجحاً
          if (!resp.ok) {
            // محاولة الحصول على رسالة الخطأ من الرد
            const errorText = await resp.text();
            throw new Error(errorText || `خطأ في الخادم: ${resp.status} ${resp.statusText}`);
          }
          
          const json = await resp.json();
          statusElem.textContent = '✅ ' + (json.message || 'تمّ تسجيل الحضور');
          statusElem.className = 'success';
          
        } catch (e) {
          // معالجة الأخطاء المختلفة
          let errorMessage = e.message;
          
          // إذا كان الرد يحتوي على HTML، نعرض رسالة مبسطة
          if (errorMessage.includes('<!DOCTYPE html>') || 
              errorMessage.includes('<html>') || 
              errorMessage.includes('</html>')) {
            errorMessage = 'حدث خطأ غير متوقع في الخادم. الرجاء مراجعة سجلات النظام.';
          }
          
          statusElem.textContent = '❌ ' + errorMessage;
          statusElem.className = 'error';
          console.error('خطأ في المسح:', e);
        }

        // إعادة التهيئة بعد 3 ثوانٍ
        setTimeout(() => {
          processing = false;
          statusElem.textContent = 'وجّه الكاميرا إلى باركود الطالب…';
          statusElem.className = '';
        }, 3000);
      });
      
     // ... الكود السابق ...

      // دالة لتسجيل الغياب
      document.getElementById('markAbsentBtn').addEventListener('click', async function() {
        const scheduleId = document.getElementById('schedule').value;
        if (!scheduleId) {
          alert('الرجاء اختيار جدول الحصة أولاً');
          return;
        }

        // تحقق من حالة التسجيل
        try {
          const statusResponse = await fetch(`/attendance/status?schedule_id=${scheduleId}`);
          const statusData = await statusResponse.json();
          
          if (statusData.marked) {
            alert('لقد قمت بتسجيل الغياب لهذه الجلسة مسبقاً');
            document.getElementById('attendanceStatus').style.display = 'block';
            return;
          }
        } catch (e) {
          console.error('خطأ في التحقق من الحالة:', e);
        }

        if (!confirm('هل أنت متأكد من تسجيل الغياب لجميع الطلاب الذين لم يحضروا؟')) {
          return;
        }

        const btn = this;
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.innerHTML = '<span class="loading"></span> جاري التسجيل...';

        try {
          const response = await fetch('/attendance/mark-absent', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ session_schedule_id: scheduleId })
          });

          const result = await response.json();
          
          if (response.ok) {
            alert(result.message);
            document.getElementById('attendanceStatus').style.display = 'block';
          } else {
            alert('❌ ' + (result.message || 'حدث خطأ أثناء تسجيل الغياب'));
          }
        } catch (error) {
          console.error('Error:', error);
          alert('حدث خطأ أثناء تسجيل الغياب');
        } finally {
          btn.disabled = false;
          btn.textContent = originalText;
        }
      });

      // التحقق من حالة التسجيل عند تغيير الجدول
      document.getElementById('schedule').addEventListener('change', async function() {
        const scheduleId = this.value;
        if (!scheduleId) return;
        
        try {
          const response = await fetch(`/attendance/status?schedule_id=${scheduleId}`);
          const data = await response.json();
          
          const statusElem = document.getElementById('attendanceStatus');
          if (data.marked) {
            statusElem.style.display = 'block';
            document.getElementById('markAbsentBtn').disabled = true;
          } else {
            statusElem.style.display = 'none';
            document.getElementById('markAbsentBtn').disabled = false;
          }
        } catch (e) {
          console.error('خطأ في التحقق من حالة التسجيل:', e);
        }
      });

    })();
</script>
</body>
</html>