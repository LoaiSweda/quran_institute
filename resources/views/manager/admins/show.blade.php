{{-- resources/views/manager/admins/show.blade.php --}}
@extends('layouts.app')
@section('title', "تفاصيل المشرف — {$admin->first_name} {$admin->last_name}")

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">
                تفاصيل المشرف: {{ $admin->first_name }} {{ $admin->last_name }}
            </h1>
            <div>
                <a href="{{ route('manager.admins.edit', $admin) }}"
                   class="btn btn-warning btn-sm me-2">
                    <i class="bi bi-pencil-square"></i> تعديل
                </a>
                <a href="{{ route('manager.admins.index') }}"
                   class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> رجوع
                </a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($admin->image)
                                <img src="{{ asset('storage/app/public/'.$admin->image) }}" class="rounded-circle" width="120" height="120" alt="">
                            @else
                                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width:120px;height:120px;">
                                    <i class="bi bi-person fs-1 text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <h5 class="mb-1">{{ $admin->first_name }} {{ $admin->last_name }}</h5>
                        <div class="text-muted">{{ $admin->user->email }}</div>
                        <div class="mt-3">
                            <span class="badge bg-primary-subtle border border-primary text-primary">Role: admin</span>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <form action="{{ route('manager.admins.destroy', $admin) }}" method="POST"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا المشرف؟');">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i> حذف
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">البيانات الأساسية</h6>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3 text-end">
                            <div class="col-md-6">
                                <strong>الاسم الكامل:</strong>
                                <div class="text-muted">{{ $admin->first_name }} {{ $admin->last_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <strong>البريد الإلكتروني:</strong>
                                <div class="text-muted">{{ $admin->user->email }}</div>
                            </div>
                            <div class="col-md-6">
                                <strong>الهاتف:</strong>
                                <div class="text-muted">{{ $admin->phone ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <strong>العنوان:</strong>
                                <div class="text-muted">{{ $admin->address ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <strong>تاريخ الميلاد:</strong>
                                <div class="text-muted">{{ $admin->birthdate?->format('Y-m-d') ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <strong>تاريخ الإنشاء:</strong>
                                <div class="text-muted">{{ $admin->created_at?->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- يمكنك إضافة كروت أخرى هنا (صلاحيات، نشاط، …) --}}
            </div>
        </div>

    </div>
@endsection
