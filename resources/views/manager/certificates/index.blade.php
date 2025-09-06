@extends('layouts.app')
@section('title', 'طلبات الشهادات')

@section('content')
    <div class="container-fluid" dir="rtl">
        <div class="card shadow-sm">
            <div class="card-header">سجل الطلبات</div>

            <div class="card-body">
                @if(isset($requests) && $requests->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-end">
                            <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>الطالب</th>
                                <th>المادة</th>
                                <th>الحالة</th>
                                <th>إجراء</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($requests as $req)
                                <tr>
                                    <td>{{ optional($req->created_at)->format('Y-m-d H:i') }}</td>
                                    <td>{{ optional($req->student)->first_name }} {{ optional($req->student)->last_name }}</td>
                                    <td>{{ optional($req->subject)->name }}</td>
                                    <td>
                                        @switch($req->status)
                                            @case('approved')
                                                <span class="badge bg-success">موافَق عليها</span>
                                                @break
                                            @case('refused')
                                                <span class="badge bg-danger">مرفوضة</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">قيد المراجعة</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($req->status === 'approved')
                                            <a href="{{ route('manager.certificates.export', $req) }}" class="btn btn-success btn-sm">
                                                <i class="bi bi-download"></i> تصدير
                                            </a>
                                        @else
                                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                                <i class="bi bi-download"></i> تصدير
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $requests->links() }}
                @else
                    <p class="text-muted mb-0">لا توجد طلبات بعد.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
