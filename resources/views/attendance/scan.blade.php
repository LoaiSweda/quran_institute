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
        
    <label for="schedule">
      اختر جدول الحصة (يوم {{ $days[\Carbon\Carbon::now('Asia/Damascus')->dayOfWeek] }}):
    </label>
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

    <button id="markAbsentBtn" class="btn btn-danger mt-3" disabled>
      تسجيل الغياب للطلاب المتبقين
    </button>
     
    <div id="attendanceStatus" class="mt-3" style="display: none;">
      <span class="badge bg-success">تم تسجيل الغياب</span>
    </div>  
  </div>

<script>
(async () => {
  const codeReader    = new ZXing.BrowserMultiFormatReader();
  const videoElem     = document.getElementById('preview');
  const statusElem    = document.getElementById('status');
  const markAbsentBtn = document.getElementById('markAbsentBtn');
  const sessionInfo   = document.getElementById('sessionInfo');
  const sessionStatus = document.getElementById('sessionStatus');
  const timeRemaining = document.getElementById('timeRemaining');

  // بدء الكاميرا
  try {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: 'environment',
        width: { ideal: 1280 },
        height:{ ideal: 720 }
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

    const qr         = result.text;
    const scheduleId = document.getElementById('schedule').value;
    if (!scheduleId) {
      statusElem.textContent = '❌ الرجاء اختيار جدول الحصة أولاً';
      statusElem.className = 'error';
      processing = false;
      return;
    }

    statusElem.textContent = `تم المسح: ${qr} — جاريّ التسجيل…`;
    statusElem.className   = '';

    try {
      const resp = await fetch('/attendance/scan', {
        method: 'POST',
        headers: {
          'Content-Type':'application/json',
          'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ qr, session_schedule_id: scheduleId })
      });

      if (!resp.ok) {
        const data = await resp.json().catch(() => null);
        throw new Error(data?.message || `خطأ ${resp.status}`);
      }

      const json = await resp.json();
      statusElem.textContent = '✅ ' + (json.message || 'تمّ تسجيل الحضور');
      statusElem.className   = 'success';

    } catch (e) {
      let msg = e.message;
      if (msg.includes('<html>')) {
        msg = 'حدث خطأ غير متوقع في الخادم. الرجاء مراجعة سجلات النظام.';
      }
      statusElem.textContent = '❌ ' + msg;
      statusElem.className   = 'error';
      console.error(e);
    }

    setTimeout(() => {
      processing = false;
      statusElem.textContent = 'وجّه الكاميرا إلى باركود الطالب…';
      statusElem.className   = '';
    }, 3000);
  });

  // عند تغيير اختيار الجدول
  document.getElementById('schedule').addEventListener('change', async function() {
    const scheduleId = this.value;
    if (!scheduleId) {
      markAbsentBtn.disabled = true;
      sessionInfo.style.display = 'none';
      return;
    }

    // 1) تحقق إذا انتهت الجلسة
    try {
      const sessRes = await fetch(`/attendance/session-status?schedule_id=${scheduleId}`);
      const sessData = await sessRes.json();
      sessionInfo.style.display = 'block';

      if (!sessData.ended) {
        markAbsentBtn.disabled = true;
        sessionStatus.textContent   = 'لم تنتهِ الجلسة بعد';
        timeRemaining.textContent   = sessData.time_remaining;
      } else {
        sessionStatus.textContent   = 'انتهت الجلسة';
        timeRemaining.textContent   = sessData.time_remaining;
        // بعد انتهاء الجلسة، نتحقق إذا تم تسجيل الغياب مسبقًا
        const attRes = await fetch(`/attendance/status?schedule_id=${scheduleId}`);
        const attData = await attRes.json();
        if (attData.marked) {
          markAbsentBtn.disabled = true;
          document.getElementById('attendanceStatus').style.display = 'block';
        } else {
          markAbsentBtn.disabled = false;
          document.getElementById('attendanceStatus').style.display = 'none';
        }
      }
    } catch (e) {
      console.error('خطأ في جلب حالة الجلسة:', e);
    }
  });

  // دالة لتسجيل الغياب (تتأكد تلقائيًا أن الزر معطل حين لا يُفترض النقر)
  markAbsentBtn.addEventListener('click', async function() {
    const scheduleId = document.getElementById('schedule').value;
    if (!scheduleId || this.disabled) return;

    if (!confirm('هل أنت متأكد من تسجيل الغياب لجميع الطلاب الذين لم يحضروا؟')) {
      return;
    }

    this.disabled = true;
    const original = this.textContent;
    this.innerHTML = '<span class="loading"></span> جاري التسجيل...';

    try {
      const resp = await fetch('/attendance/mark-absent', {
        method: 'POST',
        headers: {
          'Content-Type':'application/json',
          'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ session_schedule_id: scheduleId })
      });
      const data = await resp.json();
      alert(resp.ok ? data.message : '❌ ' + data.message);
      if (resp.ok) {
        document.getElementById('attendanceStatus').style.display = 'block';
      }
    } catch (e) {
      console.error('Error:', e);
      alert('حدث خطأ أثناء تسجيل الغياب');
    } finally {
      this.disabled   = false;
      this.textContent = original;
    }
  });

})();
</script>
</body>
</html>
