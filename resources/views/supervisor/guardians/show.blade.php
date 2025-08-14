{{-- resources/views/admin/guardians/show.blade.php --}}
@extends('layouts.app')
@section('title','تفاصيل ولي الأمر')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">تفاصيل ولي الأمر</h1>
            <a href="{{ route('admin.guardians.index', request()->only('institute_id')) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> رجوع
            </a>
        </div>

        {{-- بيانات ولي الأمر --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">الاسم كاملًا</dt>
                    <dd class="col-sm-9">{{ $guardian->name }}</dd>

                    <dt class="col-sm-3">الهاتف</dt>
                    <dd class="col-sm-9">{{ $guardian->phone }}</dd>

                    <dt class="col-sm-3">العنوان</dt>
                    <dd class="col-sm-9">{{ $guardian->address ?? '—' }}</dd>

                    <dt class="col-sm-3">البريد الإلكتروني</dt>
                    <dd class="col-sm-9">{{ $guardian->user->email ?? '—' }}</dd>
                </dl>
            </div>
        </div>

        {{-- قائمة الطلاب المرتبطين --}}
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">الطلاب المرتبطين</h5>
            </div>
            <div class="card-body p-0">
                @if($guardian->students->isEmpty())
                    <p class="text-center text-muted py-4">لا يوجد طلاب مرتبطين</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 text-end">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الهاتف</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($guardian->students as $stud)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $stud->first_name }} {{ $stud->last_name }}</td>
                                    <td>{{ $stud->phone ?? '—' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
