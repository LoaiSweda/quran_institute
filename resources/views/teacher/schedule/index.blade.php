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
                    <tr>
                        <td class="align-middle">{{ $slot }}</td>
                        @foreach(array_keys($days) as $dow)
                            <td style="min-width:120px; vertical-align: top;">
                                @foreach($sessionsByDay[$dow] ?? [] as $sess)
                                    @if(\Carbon\Carbon::parse($sess->start_time)->format('H:i') == $slot)
                                        <div class="badge bg-primary mb-1">
                                            {{ $sess->educationClass->name }}
                                        </div>
                                        <div style="font-size:.85rem;">
                                            {{ \Carbon\Carbon::parse($sess->start_time)->format('H:i') }}
                                            –
                                            {{ \Carbon\Carbon::parse($sess->end_time)->format('H:i') }}
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
