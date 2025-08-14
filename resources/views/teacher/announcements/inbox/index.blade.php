@extends('layouts.app')
@section('title','إعلانات موجّهة لي')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 text-gray-800"><i class="bi bi-inbox"></i> الإعلانات الموجّهة إليّ</h1>
            <a href="{{ route('teacher.announcements.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-bullhorn"></i> إعلاناتي (أنا الناشر)
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 text-end align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>النوع</th>
                            <th>المعهد</th>
                            <th>الوصف المختصر</th>
                            <th>ينتهي في</th>
                            <th class="text-center">عرض</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($ads as $ad)
                            <tr>
                                <td>{{ $loop->iteration + ($ads->currentPage()-1)*$ads->perPage() }}</td>
                                <td>{{ $ad->title }}</td>
                                <td>{{ $ad->type?->name ?? '—' }}</td>
                                <td>{{ $ad->institute?->name ?? 'عام' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($ad->description, 70) }}</td>
                                <td>{{ \Carbon\Carbon::parse($ad->end_date)->format('Y-m-d') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('teacher.announcements.inbox.show', $ad) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    لا توجد إعلانات موجّهة لك حالياً.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($ads->hasPages())
                <div class="card-footer">
                    {{ $ads->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
