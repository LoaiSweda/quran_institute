@extends('layouts.app')

@section('title','تعديل الإعلان')

@section('content')
    <div class="container py-2">

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h3 class="mb-0 text-primary"><i class="bi bi-pencil-fill me-2"></i>تعديل الإعلان</h3>
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> العودة
                </a>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.announcements.update', $ad) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">العنوان *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $ad->title) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="type_id" class="form-label">نوع الإعلان *</label>
                            <select id="type_id" class="form-select @error('type_id') is-invalid @enderror"
                                    name="type_id" required>
                                <option value="">اختر النوع</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ old('type_id', $ad->type_id)==$type->id?'selected':'' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea id="description" class="form-control @error('description') is-invalid @enderror"
                                  name="description" rows="4">{{ old('description', $ad->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="link" class="form-label">رابط (URL)</label>
                            <input type="url" class="form-control @error('link') is-invalid @enderror"
                                   id="link" name="link" value="{{ old('link', $ad->link) }}">
                            @error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">تاريخ الانتهاء *</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                   id="end_date" name="end_date"
                                   value="{{ old('end_date', \Carbon\Carbon::parse($ad->end_date)->format('Y-m-d')) }}"
                                   required>
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">الحالة *</label>
                            <select id="status" class="form-select @error('status') is-invalid @enderror"
                                    name="status" required>
                                <option value="active"   {{ old('status', $ad->status)=='active'   ? 'selected':'' }}>نشط</option>
                                <option value="inactive" {{ old('status', $ad->status)=='inactive' ? 'selected':'' }}>غير نشط</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">مشاهدو الإعلان *</label>
                            <div class="d-flex flex-wrap gap-2">
                                @php $selected = old('watches_roles', $ad->userAds->pluck('watches_role')->toArray()); @endphp
                                @foreach(['student'=>'طالب','guardian'=>'وصي','teacher'=>'معلم'] as $roleKey=>$roleLabel)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox"
                                               id="chk_{{ $roleKey }}" name="watches_roles[]" value="{{ $roleKey }}"
                                            {{ in_array($roleKey, $selected)?'checked':'' }}>
                                        <label class="form-check-label" for="chk_{{ $roleKey }}">{{ $roleLabel }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('watches_roles')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">تغيير صورة الإعلان</label>
                        @if($ad->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/'.$ad->image) }}" alt="صورة حالية" class="img-thumbnail" style="max-width:150px;">
                            </div>
                        @endif
                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                               id="image" name="image" accept="image/*">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
