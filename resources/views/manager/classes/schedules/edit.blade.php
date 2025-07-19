{{-- resources/views/manager/classes/schedules/edit.blade.php --}}
@extends('layouts.app')

@section('title', "تعديل موعد — {$class->name}")

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">تعديل موعد لحلقة: {{ $class->name }}</h1>

        <div class="card shadow-sm">
            <div class="card-body">

                {{-- نموذج التعديل --}}
                <form action="{{ route('manager.classes.schedules.update', [$class->id, $schedule->id]) }}"
                      method="POST">
                    @csrf @method('PUT')

                    <div class="row gy-3">
                        {{-- يوم الأسبوع --}}
                        <div class="col-md-6 text-end">
                            <label for="day_of_week" class="form-label">يوم الأسبوع</label>
                            <select name="day_of_week" id="day_of_week"
                                    class="form-select @error('day_of_week') is-invalid @enderror" required>
                                <option value="" disabled>-- اختر اليوم --</option>
                                @foreach([
                                    'Saturday'=>'السبت','Sunday'=>'الأحد','Monday'=>'الإثنين',
                                    'Tuesday'=>'الثلاثاء','Wednesday'=>'الأربعاء',
                                    'Thursday'=>'الخميس','Friday'=>'الجمعة'
                                ] as $key => $label)
                                    <option value="{{ $key }}"
                                            @selected(old('day_of_week', $schedule->day_of_week)==$key)>
                                    {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('day_of_week')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- وقت البدء --}}
                        <div class="col-md-6 text-end">
                            <label for="start_time" class="form-label">وقت البدء</label>
                            <input type="time" name="start_time" id="start_time"
                                   value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')) }}"
                                   class="form-control @error('start_time') is-invalid @enderror" required>
                            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- وقت الانتهاء --}}
                        <div class="col-md-6 text-end">
                            <label for="end_time" class="form-label">وقت الانتهاء</label>
                            <input type="time" name="end_time" id="end_time"
                                   value="{{ old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('H:i')) }}"
                                   class="form-control @error('end_time') is-invalid @enderror" required>
                            @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- أزرار الحفظ والإلغاء --}}
                    <div class="mt-4 text-end">
                        <a href="{{ route('manager.classes.edit', $class->id) }}"
                           class="btn btn-secondary">
                            إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary">
                            حفظ التعديلات
                        </button>
                    </div>
                </form>

                {{-- زرّ الحذف في نموذج منفصل --}}
                <div class="mt-3 text-end">
                    <form action="{{ route('manager.classes.schedules.destroy', [$class->id, $schedule->id]) }}"
                          method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الموعد؟');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            حذف الموعد
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- بطاقة المواعيد السابقة --}}
        <div class="card shadow-sm mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">المواعيد السابقة</h6>
            </div>
            <div class="card-body p-0">
                @if($class->sessionSchedules->isEmpty())
                    <p class="text-center text-muted py-4 mb-0">
                        لا توجد مواعيد سابقة لهذه الحلقة.
                    </p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 text-end">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>اليوم</th>
                                <th>من</th>
                                <th>إلى</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($class->sessionSchedules as $idx => $sch)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $sch->day_of_week }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('manager.classes.schedules.edit', [$class->id, $sch->id]) }}"
                                           class="btn btn-sm btn-outline-warning" title="تعديل الموعد">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('manager.classes.schedules.destroy', [$class->id, $sch->id]) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('هل تريد حذف هذا الموعد؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف الموعد">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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
@endsection
