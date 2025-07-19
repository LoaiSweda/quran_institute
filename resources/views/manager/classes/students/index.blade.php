    @extends('layouts.app')
    @section('title',"طلاب الحلقة — {$class->name}")

    @section('content')
        <div class="container-fluid">
            {{-- Header + Back --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-gray-800">طلاب الحلقة: {{ $class->name }}</h1>
                <a href="{{ route('manager.classes.show',$class) }}"
                   class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> رجوع لتفاصيل الحلقة
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">قائمة الطلاب</h6>
                    <a href="{{ route('manager.classes.students.create',$class) }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> إضافة طالب
                    </a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0 text-end">
                        <thead class="table-light">
                        <tr><th>#</th><th>الاسم</th><th>البريد</th><th class="text-center">حذف</th></tr>
                        </thead>
                        <tbody>
                        @forelse($students as $u)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional($u->studentProfile)->first_name }}
                                    {{ optional($u->studentProfile)->last_name }}
                                </td>                                <td>{{ $u->email }}</td>
                                <td class="text-center">
                                    <form action="{{ route('manager.classes.students.destroy', [$class,$u]) }}"
                                          method="POST"
                                          onsubmit="return confirm('هل تريد إزالة الطالب؟');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">
                                    لا توجد طلاب مضافين.
                                </td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection
