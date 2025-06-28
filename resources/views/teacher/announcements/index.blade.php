{{-- resources/views/teacher/announcements/index.blade.php --}}
@extends('layouts.app')

@section('title','إعلاناتي')

@section('content')
<div class="content-header">
    <h2>إعلاناتي</h2>
    <button id="toggleForm" class="btn-open">إضافة إعلان جديد</button>
</div>

{{-- form مخفي افتراضياً --}}
<div id="inlineForm" class="announcement-form">
    <h3>إضافة إعلان جديد</h3>
    <form method="POST" action="{{ route('teacher.announcements.store') }}">
        @csrf

        <div class="form-group">
            <label for="title">العنوان *</label>
            <input id="title" name="title" type="text" value="{{ old('title') }}" required>
            @error('title') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="type_id">نوع الإعلان *</label>
            <select id="type_id" name="type_id" required>
                <option value="">اختر النوع</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ old('type_id')==$type->id?'selected':'' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
            @error('type_id') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description">الوصف</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
            @error('description') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label for="link">رابط (URL)</label>
                <input id="link" name="link" type="url" value="{{ old('link') }}">
                @error('link') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group half">
                <label for="end_date">تاريخ الانتهاء *</label>
                <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" required>
                @error('end_date') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label for="status">الحالة *</label>
                <select id="status" name="status" required>
                    <option value="active" {{ old('status')=='active'?'selected':'' }}>نشط</option>
                    <option value="inactive" {{ old('status')=='inactive'?'selected':'' }}>غير نشط</option>
                </select>
                @error('status') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group half">
                <label>من سيشاهد الإعلان؟ *</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="watches_roles[]" value="student" {{ (is_array(old('watches_roles')) && in_array('student', old('watches_roles'))) ? 'checked' : '' }}> طالب</label>
                    <label><input type="checkbox" name="watches_roles[]" value="guardian" {{ (is_array(old('watches_roles')) && in_array('guardian', old('watches_roles'))) ? 'checked' : '' }}> وصي</label>
                    <label><input type="checkbox" name="watches_roles[]" value="teacher" {{ (is_array(old('watches_roles')) && in_array('teacher', old('watches_roles'))) ? 'checked' : '' }}> معلم</label>
                </div>
                @error('watches_roles') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <button type="submit" class="btn-submit">حفظ الإعلان</button>
    </form>
</div>

@if(session('success'))
    <div style="background:#e8f8f5; color:#16a085; padding:12px; border-radius:6px; margin-bottom:15px;">
        {{ session('success') }}
    </div>
@endif


<div class="table-responsive" style="overflow-x:auto;">
    <table style="width:100%; border-collapse: collapse; font-family: sans-serif;">
        <thead>
            <tr style="background: #ecf0f1; text-align:right;">
                <th style="padding: 12px; border: 1px solid #bdc3c7;">#</th>
                <th style="padding: 12px; border: 1px solid #bdc3c7;">العنوان</th>
                <th style="padding: 12px; border: 1px solid #bdc3c7;">النوع</th>
                <th style="padding: 12px; border: 1px solid #bdc3c7;">الوصف</th>
                <th style="padding: 12px; border: 1px solid #bdc3c7;">رابط</th>
                <th style="padding: 12px; border: 1px solid #bdc3c7;">تاريخ الانتهاء</th>
                <th style="padding: 12px; border: 1px solid #bdc3c7;">الحالة</th>
                <th style="width:120px; text-align:center;">إجراءات</th> {{-- جديد --}}
            </tr>
        </thead>
        <tbody>
            @forelse($ads as $ad)
                @php $status = $ad->computed_status; @endphp
                <tr style="border-bottom:1px solid #ecf0f1;">
                    <td style="padding: 10px; border: 1px solid #ecf0f1; text-align:right;">{{ $loop->iteration }}</td>
                    <td style="padding: 10px; border: 1px solid #ecf0f1;">{{ $ad->title }}</td>
                    <td style="padding: 10px; border: 1px solid #ecf0f1;">{{ $ad->type?->name ?? '—' }}</td>
                    <td style="padding: 10px; border: 1px solid #ecf0f1;">{{ \Illuminate\Support\Str::limit($ad->description, 50) }}</td>
                    <td style="padding: 10px; border: 1px solid #ecf0f1;">
                        @if($ad->link)
                            <a href="{{ $ad->link }}" target="_blank" style="color:#2980b9;">رابط الإعلان</a>
                        @else
                            —
                        @endif
                    </td>
                    <td style="padding: 10px; border: 1px solid #ecf0f1;">
                        {{ \Carbon\Carbon::parse($ad->end_date)->translatedFormat('Y-m-d') }}
                    </td>
                    <td style="padding: 10px; border: 1px solid #ecf0f1;">
                        @if($status === 'expired')
                            <span style="color:#7f8c8d;">منتهي</span>
                        @elseif($status === 'active')
                            <span style="color:#27ae60;">نشط</span>
                        @else
                            <span style="color:#c0392b;">غير نشط</span>
                        @endif
                    </td>
                    <td>
                        <!-- عرض -->
                        <a href="{{ route('teacher.announcements.show', $ad) }}"
                        class="btn btn-sm btn-outline-primary me-1" title="عرض">
                        <i class="bi bi-eye"></i>
                        </a>
                        <!-- تعديل -->
                        <a href="{{ route('teacher.announcements.edit', $ad) }}"
                        class="btn btn-sm btn-outline-warning me-1" title="تعديل">
                        <i class="bi bi-pencil-square"></i>
                        </a>
                        <!-- حذف -->
                        <form action="{{ route('teacher.announcements.destroy', $ad) }}"
                            method="POST" class="d-inline"
                            onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان؟');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                            <i class="bi bi-trash"></i>
                        </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 12px; text-align: center; color:#7f8c8d;">
                        لا توجد إعلانات منشورة بعد.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-3 px-3">
    {{ $ads->links('pagination::bootstrap-5') }}
</div>


@endsection

@push('styles')
<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.content-header h2 {
    font-size: 1.6rem;
    color: #2c3e50;
}
.btn-open {
    padding: 8px 16px;
    background: #2980b9;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background .2s;
}
.btn-open:hover {
    background: #1f5d8a;
}

/* شكل النموذج */
.announcement-form {
    display: none;
    background: #fff;
    padding: 20px 25px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}
.announcement-form h3 {
    margin-bottom: 15px;
    color: #2c3e50;
    text-align: center;
    font-size: 1.4rem;
}

/* صفوف النموذج */
.form-row {
    display: flex;
    gap: 20px;
}
.form-group {
    margin-bottom: 16px;
    flex: 1;
}
.form-group.half {
    flex: 1;
}
.form-group label {
    display: block;
    margin-bottom: 6px;
    color: #34495e;
    font-weight: 500;
}
.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #bdc3c7;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: border-color .2s;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #2980b9;
    outline: none;
}
.form-group textarea {
    min-height: 80px;
    resize: vertical;
}

/* مجموعة التشيك بوكس */
.checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}
.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    font-size: 0.95rem;
}

/* أزرار */
.btn-submit {
    background: #27ae60;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    cursor: pointer;
    width: 50%;
    margin-right: 25%;
    font-size: 1rem;
    transition: background .2s;
}
.btn-submit:hover {
    background: #1e8449;
}

/* رسائل الخطأ */
.error {
    color: #e74c3c;
    font-size: 0.85rem;
    margin-top: 4px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('toggleForm');
    const form   = document.getElementById('inlineForm');

    toggle.addEventListener('click', () => {
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
        toggle.textContent = form.style.display === 'none' ? 'إضافة إعلان جديد' : 'إلغاء';
    });
});
</script>
@endpush
