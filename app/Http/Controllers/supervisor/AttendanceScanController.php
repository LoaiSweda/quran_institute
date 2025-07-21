<?php


namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;

class AttendanceScanController extends Controller
{
    public function show()
    {
        // هذه الـ blade تعرض سكربت الـ ZXing
        return view('attendance.scan');
    }
}
