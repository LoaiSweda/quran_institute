<?php
// app/Http/Controllers/Teacher/AnnouncementController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ad;
use App\Models\AdsType;
use App\Models\UserAd;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:teacher']);
    }

    // عرض القائمة + أنواع الإعلانات
    public function index()
    {
        $ads = Ad::where('user_id', Auth::id())
            ->with('type')
            ->orderByDesc('created_at')
            ->paginate(10);

        $types = AdsType::all();

        return view('teacher.announcements.index', compact('ads','types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:150',
            'type_id'     => 'required|exists:ads_types,id',
            'description' => 'nullable|string',
            'link'        => 'nullable|url',
            'end_date'    => 'required|date|after:today',
            'status'      => 'required|in:active,inactive',
            'watches_roles'  => 'required|array',
            'watches_roles.*'=> 'in:student,guardian,teacher', 
            'image'         => 'nullable|image|max:2048',
        ]);

         // رفع الصورة
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('ads', 'public');
        }

        $data['user_id'] = Auth::id();
        $ad = Ad::create($data);

        // ننشئ سجلًا لكل دور مختار
        foreach ($data['watches_roles'] as $role) {
            UserAd::create([
                'publish_id'   => Auth::id(),
                'ads_id'       => $ad->id,
                'watches_role' => $role,
            ]);
        }

        return redirect()
            ->route('teacher.announcements.index')
            ->with('success','تم إضافة الإعلان بنجاح');
    }

     // عرض تفاصيل إعلان واحد
    public function show(Ad $ad)
    {
        return view('teacher.announcements.show', compact('ad'));
    }

    // فورم التعديل
    public function edit(Ad $ad)
    {
        $types = AdsType::all();
        return view('teacher.announcements.edit', compact('ad','types'));
    }

    // تحديث الإعلان
    public function update(Request $request, Ad $ad)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:150',
            'type_id'        => 'required|exists:ads_types,id',
            'description'    => 'nullable|string',
            'link'           => 'nullable|url',
            'end_date'       => 'required|date|after:today',
            'status'         => 'required|in:active,inactive',
            'watches_roles'  => 'required|array',
            'watches_roles.*'=> 'in:student,guardian,teacher',
            'image' => 'nullable|image|max:2048',
        ]);

        // استبدال الصورة
        if ($request->hasFile('image')) {
            // حذف القديمة إن وجدت
            if ($ad->image) {
                Storage::disk('public')->delete($ad->image);
            }
            $data['image'] = $request->file('image')->store('ads', 'public');
        }


        $ad->update($data);

        // إعادة إنشاء صلاحيات المشاهدة
        UserAd::where('ads_id', $ad->id)->delete();
        foreach ($data['watches_roles'] as $role) {
            UserAd::create([
                'publish_id'   => Auth::id(),
                'ads_id'       => $ad->id,
                'watches_role' => $role,
            ]);
        }

        return redirect()->route('teacher.announcements.index')
                         ->with('success','تم تحديث الإعلان بنجاح');
    }

    // حذف الإعلان
    public function destroy(Ad $ad)
    {
        // يحذف السجلات المرتبطة أولاً
        UserAd::where('ads_id', $ad->id)->delete();
        $ad->delete();

        return redirect()->route('teacher.announcements.index')
                         ->with('success','تم حذف الإعلان بنجاح');
    }
}
