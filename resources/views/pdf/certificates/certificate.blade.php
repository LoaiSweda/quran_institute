<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>شهادة إنهاء مادة - Certificate of Completion</title>
    <style>
        /* صفحة واحدة بمقاس مخصص: 200mm × 280mm */
        @page {
            size: 200mm 280mm;
            margin: 8mm;
        }
        html, body {
            margin: 0;
            padding: 0;
            color: #2c3e50;
            background: #ffffff;
            font-family: "DejaVu Sans", sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .certificate-frame {
            border: 2px solid #D4AF37;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 12mm 16mm;
            position: relative;
            min-height: calc(280mm - 16mm);
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-sizing: border-box;
            background: linear-gradient(to bottom, #fcfcfc, #f5f5f5);
        }
        .watermark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            opacity: 0.04;
            color: #2c3e50;
            font-weight: bold;
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
        }
        .brand { text-align: center; margin-bottom: 6mm; position: relative; z-index: 1; }
        .brand img { max-height: 28mm; display: block; margin: 0 auto 4mm; }
        .title {
            text-align: center; font-size: 26pt; font-weight: 700;
            margin: 2mm 0 1mm; letter-spacing: 1px;
            color: #1a5276; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .subtitle { text-align: center; font-size: 12pt; color: #7f8c8d; margin-bottom: 8mm; }

        .content {
            text-align: center; line-height: 2.2; font-size: 14pt;
            margin: 0 auto 8mm; max-width: 85%; position: relative; z-index: 1;
        }
        .hl {
            display: inline-block; padding: 0 4mm; border-bottom: 2px solid #D4AF37;
            font-weight: 700; color: #D4AF37;
            text-shadow: 0.5px 0.5px 1px rgba(0,0,0,0.1);
        }

        .meta {
            margin-top: 4mm; display: flex; justify-content: space-around;
            font-size: 11pt; color: #7f8c8d; padding: 3mm 4mm;
            background: #f1f1f1; border-radius: 3mm;
            border: 1px dashed #d4d4d4;
        }
        .meta strong { color: #555; }


        .table-two-col { width: 100%; border-collapse: collapse; margin-top: 8mm; }
        .table-two-col td { width: 50%; vertical-align: bottom; padding: 0 6mm; }
        .cell-right { text-align: right; }
        .cell-left  { text-align: left;  }

        /* منطقة الختم */
        .signature-area { height: 20mm; margin-bottom: 4mm; position: relative; }
        .signature-stamp {
            position: absolute; bottom: 0;
            width: 42mm; height: 25mm;
            border: 2px dashed #D4AF37; border-radius: 3mm;
            display: inline-flex; align-items: center; justify-content: center;
            color: #D4AF37; font-size: 10pt; background: rgba(255,255,255,.7);
            z-index: 0; pointer-events: none;
        }
        /* ألصق الختم لليمين/اليسار لكل خلية */
        .cell-right .signature-stamp { right: 0; }
        .cell-left  .signature-stamp { left: 0;  }

        /* سطر التوقيع */
        .sign-line { border-top: 1px solid #bdc3c7; padding-top: 3mm; font-size: 10pt; color: #7f8c8d; }

        .footer {
            margin-top: 6mm; font-size: 9pt; color: #95a5a6;
            display: flex; justify-content: space-between;
            padding-top: 3mm; border-top: 1px solid #ecf0f1;
        }
        .certificate-id { position: absolute; top: 10mm; left: 10mm; font-size: 8pt; color: #95a5a6; }

        .decoration { position: absolute; inset: 0; pointer-events: none; z-index: 0; }
        .corner {
            position: absolute; width: 30px; height: 30px; border-style: solid;
            border-color: #D4AF37; border-width: 0;
        }
        .corner-tl { top: 0; left: 0; border-top-width: 30px; border-left-width: 30px; border-radius: 0 0 100% 0; }
        .corner-tr { top: 0; right: 0; border-top-width: 30px; border-right-width: 30px; border-radius: 0 0 0 100%; }
        .corner-bl { bottom: 0; left: 0; border-bottom-width: 30px; border-left-width: 30px; border-radius: 0 100% 0 0; }
        .corner-br { bottom: 0; right: 0; border-bottom-width: 30px; border-right-width: 30px; border-radius: 100% 0 0 0; }

        * { page-break-inside: avoid !important; }

        .svg-decoration {
            position: absolute; width: 60mm; height: 60mm; color: #D4AF37; opacity: 0.2;
        }
        .svg-tl { top: -10mm; left: -10mm; }
        .svg-tr { top: -10mm; right: -10mm; }
        .svg-bl { bottom: -10mm; left: -10mm; }
        .svg-br { bottom: -10mm; right: -10mm; }
    </style>
</head>
<body>
<div class="certificate-frame">
    <!-- زينة الزوايا SVG -->
    <svg class="svg-decoration svg-tl" viewBox="0 0 100 100" preserveAspectRatio="none">
        <path d="M 0 0 C 30 0, 0 30, 0 50 C 0 80, 50 100, 100 100" fill="none" stroke="currentColor" stroke-width="2"/>
    </svg>
    <svg class="svg-decoration svg-tr" viewBox="0 0 100 100" preserveAspectRatio="none">
        <path d="M 100 0 C 70 0, 100 30, 100 50 C 100 80, 50 100, 0 100" fill="none" stroke="currentColor" stroke-width="2"/>
    </svg>
    <svg class="svg-decoration svg-bl" viewBox="0 0 100 100" preserveAspectRatio="none">
        <path d="M 0 100 C 30 100, 0 70, 0 50 C 0 20, 50 0, 100 0" fill="none" stroke="currentColor" stroke-width="2"/>
    </svg>
    <svg class="svg-decoration svg-br" viewBox="0 0 100 100" preserveAspectRatio="none">
        <path d="M 100 100 C 70 100, 100 70, 100 50 C 100 20, 50 0, 0 0" fill="none" stroke="currentColor" stroke-width="2"/>
    </svg>

    <div class="certificate-id">ID: NO_{{ $certificate->id }}</div>

    <div class="brand">
        @if(!empty($institute->logo_url ?? null))
            <img src="{{ $institute->logo_url }}" alt="Logo">
        @endif
        <div class="title">شـهـادة إنـهـاء مـادة</div>
        <div class="subtitle">{{ $institute->name ?? 'المعهد' }}</div>
    </div>

    <div class="content">
        يُشهد بأن
        <span class="hl">{{ $student?->first_name }} {{ $student?->last_name }}</span>
        قد أتم بنجاح متطلبات مادة
        <span class="hl">{{ $subject?->name }}</span>
        وذلك بتاريخ
        <span class="hl">{{ $issued_at->translatedFormat('Y-m-d') }}</span>
        وقد أدى فيها بما يرضي الأساتذة والإدارة.
    </div>

    <div class="meta">
        <div>رقم الشهادة: <strong>{{ $certificate->id }}</strong></div>
        <div>كود المادة: <strong>#{{ $subject?->id }}</strong></div>
        <div>رقم الطالب: <strong>#{{ $student?->id }}</strong></div>
    </div>

    {{-- سطر الأختام --}}
    {{-- سطر الأختام: يمين = ختم المؤسسة ، يسار = ختم المعهد --}}
    <table class="table-two-col">
        <tr>
            <td class="cell-right">
                <div class="signature-area">
                    <div class="signature-stamp">
                        @php
                            $adminStamp = $admin?->institution_stamp_url
                                ?? ($admin?->institution_stamp_path ? asset('storage/'.$admin->institution_stamp_path) : null);
                        @endphp
                        @if($adminStamp)
                            <img src="{{ $adminStamp }}" style="position:absolute;inset:0;object-fit:contain;opacity:.9;">
                        @endif
                        ختم المؤسسة
                    </div>
                </div>
            </td>

            <td class="cell-left">
                <div class="signature-area">
                    <div class="signature-stamp">
                        @php
                            $instStamp = $institute?->institute_stamp_url
                                ?? ($institute?->institute_stamp_path ? asset('storage/'.$institute->institute_stamp_path) : null);
                        @endphp
                        @if($instStamp)
                            <img src="{{ $instStamp }}" style="position:absolute;inset:0;object-fit:contain;opacity:.9;">
                        @endif
                        ختم المعهد
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        <div>العنوان:{{ $institute->address ?? '' }}</div>
        <div>هاتف: {{ $institute->phone ?? '' }}</div>
        <div>البريد الإلكتروني: {{ $institute->email ?? '' }}</div>
    </div>
</div>
</body>
</html>
