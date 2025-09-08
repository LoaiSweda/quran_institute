@extends('layouts.app')

@section('title', 'طلاب المعهد - ' . $institute->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header bg-white p-4 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 text-primary">طلاب المعهد: {{ $institute->name }}</h2>
                            <p class="text-muted mb-0"> جميع طلاب المعهد</p>
                        </div>
                        <div>
                            <a href="{{ route('super-admin.institutes.Details', $institute) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-2"></i> العودة للتفاصيل
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>قائمة الطلاب ({{ $students->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($students->isEmpty())
                            <p class="text-center text-muted p-4">لا يوجد طلاب في هذا المعهد</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>صورة</th>
                                        <th>اسم الطالب</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الهاتف</th>
                                        <th>الفصول</th>
                                        <th>ولي الأمر</th>
                                        <th>نسبة الحضور</th>
                                        <th>النقاط</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($students as $student)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if($student->image)
                                                    <img src="{{ asset('storage/app/public/' . $student->image) }}" alt="{{ $student->first_name }}" class="rounded-circle" width="40" height="40">
                                                @else
                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                            <td>{{ $student->user->email ?? 'غير متوفر' }}</td>
                                            <td>{{ $student->phone ?? 'غير متوفر' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $student->classes->count() }}</span>
                                            </td>
                                            <td>
                                                @if($student->guardian)
                                                    {{ $student->guardian->firstname }} {{ $student->guardian->lastname }}
                                                @else
                                                    غير معين
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 10px; width: 100px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                         style="width: {{ $student->present_percentage }}%;"
                                                         aria-valuenow="{{ $student->present_percentage }}"
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small>{{ $student->present_percentage }}%</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">{{ $student->points }}</span>
                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
