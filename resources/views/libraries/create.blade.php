{{-- resources/views/libraries/create.blade.php --}}
@extends('layouts.app')

@section('title','إضافة مورد جديد')

@section('content')
<div class="content-header">
    <h2>إضافة مورد جديد للمكتبة</h2>

    <div class="d-flex gap-2">
        <a href="{{ route(
            Auth::user()->hasRole('super admin') ? 'super-admin.library.index' :
            (Auth::user()->hasRole('institute manager') ? 'manager.library.index' :
            (Auth::user()->hasRole('admin') ? 'admin.library.index' : 'teacher.library.index'))
        ) }}" class="btn-open" style="background:#7f8c8d">إلغاء</a>

    </div>
</div>

@if ($errors->any())
    <div style="background:#fdecea;color:#b71c1c;border:1px solid #f5c6cb;padding:12px 16px;border-radius:8px;margin-bottom:16px">
        <strong>حدثت أخطاء في الإدخال:</strong>
        <ul style="margin:8px 0 0 0;padding-right:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- النموذج (مفتوح افتراضياً في صفحة create) --}}
<div id="inlineForm" class="announcement-form" style="display:block">
    <h3>بيانات المورد</h3>

    <form method="POST" action="{{ route(
        Auth::user()->hasRole('super admin') ? 'super-admin.library.store' :
        (Auth::user()->hasRole('institute manager') ? 'manager.library.store' :
        (Auth::user()->hasRole('admin') ? 'admin.library.store' : 'teacher.library.store'))
    ) }}" enctype="multipart/form-data">

        @csrf

        <div class="form-row">
            <div class="form-group half">
                <label for="name">اسم المورد *</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group half">
                <label for="category_id">التصنيف</label>
                <select id="category_id" name="category_id">
                    <option value="">— اختر تصنيفاً —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label for="author">المؤلف (اختياري)</label>
                <input id="author" name="author" type="text" value="{{ old('author') }}">
                @error('author') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group half">
                <label for="isbn">رقم ISBN (اختياري)</label>
                <input id="isbn" name="isbn" type="text" value="{{ old('isbn') }}">
                @error('isbn') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="description">الوصف</label>
            <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
            @error('description') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="file">الملف (PDF, MP3, MP4, DOC, PPT, XLS) — الحد الأقصى 100MB *</label>
            <input id="file" name="file" type="file" accept=".pdf,.mp3,.mp4,.doc,.docx,.ppt,.pptx,.xls,.xlsx" required>
            @error('file') <div class="error">{{ $message }}</div> @enderror
        </div>

        @if(Auth::user()->hasAnyRole(['super admin','institute manager','admin']))
            <div class="form-group">
                <label class="d-block">إعدادات الظهور</label>
                <label style="display:flex;align-items:center;gap:8px">
                    <input type="checkbox" name="is_visible" {{ old('is_visible') ? 'checked' : '' }}>
                    مرئي للطلاب
                </label>
            </div>
        @endif

        <button type="submit" class="btn-submit">حفظ المورد</button>
    </form>
</div>
@endsection

@push('styles')
<style>
.content-header{
    display:flex;justify-content:space-between;align-items:center;margin-bottom:20px
}
.content-header h2{font-size:1.6rem;color:#2c3e50;margin:0}
.btn-open{padding:8px 16px;background:#2980b9;color:#fff;border:none;border-radius:6px;cursor:pointer;transition:.2s}
.btn-open:hover{background:#1f5d8a}

/* نفس ستايل الإعلان */
.announcement-form{
    background:#fff;padding:20px 25px;border-radius:10px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);margin-bottom:20px
}
.announcement-form h3{margin-bottom:15px;color:#2c3e50;text-align:center;font-size:1.4rem}
.form-row{display:flex;gap:20px}
.form-group{margin-bottom:16px;flex:1}
.form-group.half{flex:1}
.form-group label{display:block;margin-bottom:6px;color:#34495e;font-weight:500}
.form-group input,.form-group select,.form-group textarea{
    width:100%;padding:10px 12px;border:1px solid #bdc3c7;border-radius:6px;font-size:.95rem;transition:border-color .2s
}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:#2980b9;outline:none}
.form-group textarea{min-height:90px;resize:vertical}
.btn-submit{background:#27ae60;color:#fff;border:none;padding:10px 18px;border-radius:6px;cursor:pointer;width:50%;margin-right:25%;font-size:1rem;transition:.2s}
.btn-submit:hover{background:#1e8449}
.error{color:#e74c3c;font-size:.85rem;margin-top:4px}
@media (max-width:768px){.form-row{flex-direction:column}.btn-submit{width:100%;margin:0}}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('toggleForm');
    const form   = document.getElementById('inlineForm');
    toggle.addEventListener('click', () => {
        const visible = form.style.display !== 'none';
        form.style.display = visible ? 'none' : 'block';
        toggle.textContent = visible ? 'إظهار النموذج' : 'إخفاء النموذج';
    });
});
</script>
@endpush
