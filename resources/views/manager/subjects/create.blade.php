{{-- resources/views/manager/subjects/create.blade.php --}}
@extends('layouts.app')
@section('title','إضافة مادة جديدة')

@section('content')
    <div class="container-fluid">
        {{-- Header + Cancel Button --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 text-gray-800">إضافة مادة جديدة</h2>
            <button onclick="history.back()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-lg"></i> إلغاء
            </button>
        </div>

        {{-- Inline Form --}}
        <div class="card shadow-sm mb-4 p-4">
            <form action="{{ route('manager.subjects.store') }}" method="POST" class="row g-3">
                @csrf

                {{-- Name --}}
                <div class="col-md-6">
                    <label for="name" class="form-label">اسم المادة <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           class="form-control form-control-sm @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Level --}}
                <div class="col-md-6">
                    <label for="level" class="form-label">المستوى</label>
                    <input type="text" id="level" name="level" value="{{ old('level') }}"
                           class="form-control form-control-sm @error('level') is-invalid @enderror">
                    @error('level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Description --}}
                <div class="col-12">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea id="description" name="description" rows="3"
                              class="form-control form-control-sm @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Dates & Degree & Sessions --}}
                <div class="col-md-3">
                    <label for="start_date" class="form-label">تاريخ البداية</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}"
                           class="form-control form-control-sm @error('start_date') is-invalid @enderror">
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">تاريخ النهاية</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}"
                           class="form-control form-control-sm @error('end_date') is-invalid @enderror">
                    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label for="degree" class="form-label">الدرجة</label>
                    <input type="number" step="0.01" id="degree" name="degree" value="{{ old('degree') }}"
                           class="form-control form-control-sm @error('degree') is-invalid @enderror">
                    @error('degree')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label for="total_sessions" class="form-label">إجمالي الحلقات</label>
                    <input type="number" id="total_sessions" name="total_sessions" value="{{ old('total_sessions') }}"
                           class="form-control form-control-sm @error('total_sessions') is-invalid @enderror">
                    @error('total_sessions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Active Checkbox --}}
                <div class="col-12">
                    <input type="hidden" name="is_active" value="0">
                    <div class="form-check">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               class="form-check-input" {{ old('is_active') ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">نشط</label>
                    </div>
                    @error('is_active')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                {{-- Submit --}}
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> حفظ المادة
                    </button>
                </div>
            </form>
        </div>

        {{-- Existing Subjects List --}}
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">المواد المنشأة حديثًا</h5>
            </div>
            <div class="card-body p-0">
                @if($subjects->count())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 text-end">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>بداية</th>
                                <th>نهاية</th>
                                <th>الحالة</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($subjects as $subject)
                                <tr>
                                    <td>{{ $loop->iteration + ($subjects->perPage() * ($subjects->currentPage()-1)) }}</td>
                                    <td>{{ $subject->name }}</td>
                                    <td>{{ $subject->start_date?->format('Y-m-d') ?? '—' }}</td>
                                    <td>{{ $subject->end_date?->format('Y-m-d') ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $subject->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $subject->is_active ? 'نشط' : 'معطل' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('manager.subjects.edit', $subject) }}"
                                           class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('manager.subjects.toggle', $subject) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-{{ $subject->is_active ? 'secondary' : 'success' }}"
                                                    title="{{ $subject->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                <i class="bi bi-toggle-{{ $subject->is_active ? 'off' : 'on' }}"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="p-4 text-center text-muted">لا توجد مواد منشأة بعد.</p>
                @endif
            </div>
            @if($subjects->hasPages())
                <div class="card-footer">
                    {{ $subjects->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .content-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
        .btn-open { display:none; }
    </style>
@endpush
