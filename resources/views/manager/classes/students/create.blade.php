{{-- resources/views/manager/classes/students/create.blade.php --}}
@extends('layouts.app')
@section('title',"إضافة طالب — {$class->name}")

@section('content')
    <div class="container-fluid">

        {{-- Header + Back --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">إضافة طالب لحلقة: {{ $class->name }}</h1>
            <a href="{{ route('manager.classes.students.index', $class) }}"
               class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> رجوع
            </a>
        </div>

        {{-- Card للبحث والجدول --}}
        <div class="card shadow-sm">
            <div class="card-header py-3 d-flex align-items-center">
                <i class="bi bi-people-fill me-2"></i>
                <h6 class="m-0 font-weight-bold text-primary">اختر طالباً من قائمة طلاب المعهد</h6>
            </div>
            <div class="card-body">
                {{-- حقل البحث --}}
                <div class="mb-3">
                    <input id="student-filter" type="text"
                           class="form-control"
                           placeholder="ابحث بالاسم أو البريد…">
                </div>

                {{-- جدول الطلاب --}}
                <div class="table-responsive">
                    <table class="table table-hover text-end" id="students-table">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد</th>
                            <th class="text-center">إضافة</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($students as $i => $stu)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $stu->first_name }} {{ $stu->last_name }}</td>                                <td>{{ $stu->email }}</td>
                                <td class="text-center">
                                    <form action="{{ route('manager.classes.students.store', $class) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $stu->id }}">
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-primary"
                                                title="أضف الطالب">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        @if($students->isEmpty())
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    لا يوجد طلاب متاحين للإضافة.
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // فلترة الجدول مباشرة عند الكتابة
        document.getElementById('student-filter').addEventListener('input', function(){
            const q = this.value.trim().toLowerCase();
            document
                .querySelectorAll('#students-table tbody tr')
                .forEach(row => {
                    const name = row.children[1].textContent.toLowerCase();
                    const email = row.children[2].textContent.toLowerCase();
                    row.style.display = (name.includes(q) || email.includes(q))
                        ? '' : 'none';
                });
        });
    </script>
@endpush
