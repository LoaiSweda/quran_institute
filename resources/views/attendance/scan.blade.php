{{-- resources/views/attendance/scan.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>مسح باركود حضور الطلاب</title>
  <!-- مكتبة ZXing لمسح QR عبر جافاسكربت -->
  <script src="https://unpkg.com/@zxing/library@latest"></script>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body { font-family: sans-serif; text-align: center; padding: 1rem; }
    video { width: 100%; max-width: 400px; border: 1px solid #ccc; }
    #status { margin-top: 1rem; font-size: 1.1rem; }
    select { padding: 0.5rem; font-size: 1rem; margin-bottom: 1rem; }
  </style>
</head>
<body>
  <h1>مسح باركود حضور الطلاب</h1>

    @php
        // إذا كنت فعلاً تحفظ 0=الأحد … 6=السبت
        $days = [
            0 => 'الأحد', 1 => 'الإثنين', 2 => 'الثلاثاء',
            3 => 'الأربعاء',4 => 'الخميس',5 => 'الجمعة',
            6 => 'السبت',
        ];
    @endphp

    <label for="schedule">اختر جدول الحصة (يوم {{ $days[\Carbon\Carbon::now()->dayOfWeek] }}):</label>
    <select id="schedule">
    <option value="">-- اختر --</option>
    @foreach($schedules as $sch)
        <option value="{{ $sch->id }}">
        الصف: {{ $sch->educationClass->name }}
        | {{ $days[$sch->day_of_week] }}
        | {{ $sch->start_time->format('H:i') }} – {{ $sch->end_time->format('H:i') }}
        </option>
    @endforeach
    </select>

  {{-- 2) الفيديو للكاميرا --}}
  <video id="preview" autoplay muted playsinline></video>
  <div id="status">وجّه الكاميرا إلى باركود الطالب…</div>

  <script>
    (async () => {
      const codeReader = new ZXing.BrowserMultiFormatReader();
      const videoElem = document.getElementById('preview');
      const statusElem = document.getElementById('status');

      // ابدأ الكاميرا الخلفية
      try {
        const stream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: 'environment' }
        });
        videoElem.srcObject = stream;
      } catch (e) {
        statusElem.textContent = 'خطأ في الوصول إلى الكاميرا';
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
          processing = false;
          return;
        }

        statusElem.textContent = `تم المسح: ${qr} — جاريّ التسجيل…`;

        try {
          const resp = await fetch('/attendance/scan', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
              qr: qr,
              session_schedule_id: scheduleId
            })
          });
          const json = await resp.json();
          statusElem.textContent = resp.ok
            ? '✅ ' + (json.message || 'تمّ تسجيل الحضور')
            : '❌ ' + (json.message || resp.status);
        } catch (e) {
          statusElem.textContent = '❌ خطأ في الاتصال';
          console.error(e);
        }

        // أعد التهيئة بعد 3 ثوانٍ
        setTimeout(() => {
          processing = false;
          statusElem.textContent = 'وجّه الكاميرا إلى باركود الطالب…';
        }, 3000);
      });
    })();
  </script>
</body>
</html>
