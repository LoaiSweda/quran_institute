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
        <form method="POST" action="{{ route('manager.announcements.store') }}" enctype="multipart/form-data">
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
                        <label><input type="checkbox" name="watches_roles[]" value="admin" {{ (is_array(old('watches_roles')) && in_array('admin', old('watches_roles'))) ? 'checked' : '' }}> مشرف</label>
                        <label><input type="checkbox" name="watches_roles[]" value="manager" {{ (is_array(old('watches_roles')) && in_array('manager', old('watches_roles'))) ? 'checked' : '' }}> مدير</label>
                    </div>
                    @error('watches_roles') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group mt-3">
                <label for="image" class="form-label">صورة الإعلان</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror"
                       id="image" name="image" accept="image/*">
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn-submit">حفظ الإعلان</button>
        </form>
    </div>

    @if(session('success'))
        <div style="background:#e8f8f5; color:#16a085; padding:12px; border-radius:6px; margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">الإعلانات الموجهة</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light text-end">
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>النوع</th>
                        <th>الوصف</th>
                        <th>رابط</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الحالة</th>
                        <th class="text-center">صورة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($ads as $ad)
                        @php $status = $ad->computed_status; @endphp
                        <tr>
                            <td class="text-end">{{ $loop->iteration }}</td>
                            <td>{{ $ad->title }}</td>
                            <td>{{ $ad->type?->name ?? '—' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($ad->description, 50) }}</td>
                            <td>
                                @if($ad->link)
                                    <a href="{{ $ad->link }}" target="_blank">رابط الإعلان</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($ad->end_date)->translatedFormat('Y-m-d') }}</td>
                            <td>
                                @if($status === 'expired')
                                    <span class="badge bg-secondary">منتهي</span>
                                @elseif($status === 'active')
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($ad->image)
                                    <img src="{{ asset('storage/app/public/'.$ad->image) }}" alt="صورة الإعلان"
                                         class="img-thumbnail" style="max-width:60px;">
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('manager.announcements.show', $ad) }}"
                                   class="btn btn-sm btn-outline-primary" title="عرض">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('manager.announcements.edit', $ad) }}"
                                   class="btn btn-sm btn-outline-warning" title="تعديل">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('manager.announcements.destroy', $ad) }}"
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
                            <td colspan="9" class="text-center py-4 text-muted">
                                لا توجد إعلانات منشورة بعد.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(isset($ads) && $ads instanceof \Illuminate\Pagination\AbstractPaginator && $ads->hasPages())
            <div class="card-footer">
                {{ $ads->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .content-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
        .content-header h2{font-size:1.6rem;color:#2c3e50}
        .btn-open{padding:8px 16px;background:#2980b9;color:#fff;border:none;border-radius:6px;cursor:pointer;transition:background .2s}
        .btn-open:hover{background:#1f5d8a}
        .announcement-form{display:none;background:#fff;padding:20px 25px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.1);margin-bottom:20px}
        .announcement-form h3{margin-bottom:15px;color:#2c3e50;text-align:center;font-size:1.4rem}
        .form-row{display:flex;gap:20px}
        .form-group{margin-bottom:16px;flex:1}
        .form-group.half{flex:1}
        .form-group label{display:block;margin-bottom:6px;color:#34495e;font-weight:500}
        .form-group input,.form-group select,.form-group textarea{width:100%;padding:10px 12px;border:1px solid #bdc3c7;border-radius:6px;font-size:.95rem;transition:border-color .2s}
        .form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:#2980b9;outline:none}
        .form-group textarea{min-height:80px;resize:vertical}
        .checkbox-group{display:flex;flex-wrap:wrap;gap:15px}
        .checkbox-group label{display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.95rem}
        .btn-submit{background:#27ae60;color:#fff;border:none;padding:10px 18px;border-radius:6px;cursor:pointer;width:50%;margin-right:25%;font-size:1rem;transition:background .2s}
        .btn-submit:hover{background:#1e8449}
        .error{color:#e74c3c;font-size:.85rem;margin-top:4px}
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
