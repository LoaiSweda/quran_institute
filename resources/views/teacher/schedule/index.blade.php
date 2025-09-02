@extends('layouts.app')

@section('title','الجدول الأسبوعي')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">الجدول الأسبوعي</h1>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered text-center mb-0">
            <thead class="table-light">
                <tr>
                    <th>الوقت \ اليوم</th>
                    @foreach($days as $dow => $dayName)
                        <th>{{ $dayName }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($timeSlots as $slot)
                    @php
                        // ساعة الخانة الحالية (مثلاً "21:00" => 21)
                        $slotHour = (int) substr($slot, 0, 2);
                    @endphp
                    <tr>
                        <td class="align-middle">{{ $slot }}</td>

                        @foreach(array_keys($days) as $dow)
                            <td style="min-width:140px; vertical-align: top;">
                                @foreach(($sessionsByDay[$dow] ?? collect()) as $sess)
                                    @php
                                        // ✅ بدلاً من التطابق التام مع "H:i"
                                        // نعرض الجلسة إذا كانت ساعة بدايتها = ساعة الخانة
                                        $sessHour = (int) $sess->start_time->format('H');
                                    @endphp

                                    @if($sessHour === $slotHour)
                                        <div class="badge bg-primary mb-1">
                                            {{ $sess->educationClass->name }}
                                        </div>
                                        <div style="font-size:.85rem;">
                                            {{ $sess->start_time->format('H:i') }}
                                            –
                                            {{ $sess->end_time->format('H:i') }}
                                        </div>
                                    @endif
                                @endforeach
                            </td>
                        @endforeach

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
