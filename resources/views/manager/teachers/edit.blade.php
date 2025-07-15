@extends('layouts.app')
@section('title','تعديل بيانات المدرس')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">تعديل بيانات المدرس</h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('manager.teachers.update', $teacher) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row gy-3">
                        <div class="col-md-6 text-end">
                            <label class="form-label">الصورة الحالية</label><br>
                            @if($teacher->image)
                                <img src="{{ asset('storage/'.$teacher->image) }}"
                                     width="60" height="60" class="rounded-circle">
                            @else
                                —
                            @endif
                        </div>

                        <div class="col-md-6 text-end">
                            <label class="form-label">تغيير الصورة</label>
                            <input type="file" name="image"
                                   class="form-control @error('image') is-invalid @enderror">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 text-end">
                            <label class="form-label">الاسم الأول</label>
                            <input type="text" name="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', $teacher->first_name) }}">
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 text-end">
                            <label class="form-label">اسم العائلة</label>
                            <input type="text" name="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', $teacher->last_name) }}">
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 text-end">
                            <label class="form-label">الهاتف</label>
                            <input type="text" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $teacher->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 text-end">
                            <label class="form-label">العنوان</label>
                            <input type="text" name="address"
                                   class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address', $teacher->address) }}">
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 text-end">
                            <label class="form-label">تاريخ الميلاد</label>
                            <input type="date" name="birthdate"
                                   class="form-control @error('birthdate') is-invalid @enderror"
                                   value="{{ old('birthdate', $teacher->birthdate) }}">
                            @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('manager.teachers.show', $teacher) }}" class="btn btn-secondary">
                            إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary">
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
