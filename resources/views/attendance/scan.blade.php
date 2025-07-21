<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>مسح باركود حضور الطلاب</title>
  <!-- ZXing لمسح QR عبر جافاسكربت -->
  <script src="https://unpkg.com/@zxing/library@latest"></script>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body { font-family: sans-serif; text-align: center; padding: 1rem; }
    video { width: 100%; max-width: 400px; border: 1px solid #ccc; }
    #status { margin-top: 1rem; font-size: 1.1rem; }
  </style>
</head>
<body>
  <h1>مسح باركود الطالب</h1>
  <video id="preview" autoplay muted playsinline></video>
  <div id="status">وجّه الكاميرا إلى باركود الطالب…</div>

  <script>
    (async () => {
      const codeReader = new ZXing.BrowserMultiFormatReader();
      const videoElem = document.getElementById('preview');
      const statusElem = document.getElementById('status');

      // 1) ابدأ الكاميرا الخلفية
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

      // 2) ابدأ المسح المستمر
      let processing = false;
      codeReader.decodeFromVideoElementContinuously(videoElem, async (result, err) => {
        if (processing || !result) return;
        processing = true;

        const qr = result.text;
        statusElem.textContent = `تم المسح: ${qr} — جاريّ التسجيل…`;

        try {
          // 3) استدعاء الـ API مع الجلسة
          const resp = await fetch('/attendance/scan', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
              qr: qr,
              session_schedule_id: {{ request()->get('session_schedule_id', 17) }}
            })
          });

          const json = await resp.json();
          if (resp.ok) {
            statusElem.textContent = '✅ ' + (json.message || 'تمّ تسجيل الحضور');
          } else {
            statusElem.textContent = '❌ ' + (json.message || resp.status);
          }
        } catch (e) {
          statusElem.textContent = '❌ خطأ في الاتصال';
          console.error(e);
        }

        // 4) إعادة تمكين المسح بعد 3 ثوانٍ
        setTimeout(() => {
          processing = false;
          statusElem.textContent = 'وجّه الكاميرا إلى باركود الطالب…';
        }, 3000);
      });
    })();
  </script>
</body>
</html>
