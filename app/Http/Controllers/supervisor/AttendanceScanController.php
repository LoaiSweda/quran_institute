<?php
// app/Http/Controllers/Web/AttendanceScanController.php

namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;

class AttendanceScanController extends Controller
{
    /**
     * عرض صفحة مسح الباركود للمشرف
     */
    public function show()
    {
        return view('attendance.scan');
    }
}
