<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // معهد المدير (مالك المعهد)
        $inst = Institute::where('user_id', $user->id)->firstOrFail();

        return view('manager.profile.show', [
            'user' => $user,
            'inst' => $inst,
        ]);
    }
}
