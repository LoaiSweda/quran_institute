@extends('layouts.app')

@section('title', 'أولياء الأمور - ' . $institute->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header bg-white p-4 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 text-primary">أولياء أمور المعهد: {{ $institute->name }}</h2>
                            <p class="text-muted mb-0">  جميع أولياء أمور المعهد</p>
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
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>قائمة أولياء الأمور ({{ $guardians->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($guardians->isEmpty())
                            <p class="text-center text-muted p-4">لا يوجد أولياء أمور في هذا المعهد</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم ولي الأمر</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الهاتف</th>
                                        <th>العنوان</th>
                                        <th>عدد الأبناء</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($guardians as $guardian)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $guardian->firstname }} {{ $guardian->lastname }}</td>
                                            <td>{{ $guardian->user->email ?? 'غير متوفر' }}</td>
                                            <td>{{ $guardian->phone ?? 'غير متوفر' }}</td>
                                            <td>{{ $guardian->address ?? 'غير متوفر' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $guardian->students->count() }}</span>
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
