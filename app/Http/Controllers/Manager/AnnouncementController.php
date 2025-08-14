<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdsType;
use App\Models\Institute;
use App\Models\UserAd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:institute manager']);
    }

    /** معهد المدير الحالي (مالك المعهد) */
    protected function currentInstitute(): Institute
    {
        return Institute::where('user_id', Auth::id())->firstOrFail();
    }

    /** تحقق الملكية + النطاق (المعهد) */
    protected function ensureScope(Ad $ad): void
    {
        $inst = $this->currentInstitute();
        abort_if($ad->user_id !== Auth::id(), 403, 'ليس لديك صلاحية على هذا الإعلان.');
        abort_if((int)$ad->institute_id !== (int)$inst->id, 403, 'هذا الإعلان خارج نطاق معهدك.');
    }

    /** قائمة إعلانات المدير داخل معهدِه فقط */
    public function index()
    {
        $inst = $this->currentInstitute();

        $ads = Ad::where('user_id', Auth::id())
            ->where('institute_id', $inst->id) // مدير يدير فقط ما يخص معهدَه
            ->with('type')
            ->latest('created_at')
            ->paginate(10);

        $types = AdsType::all();

        return view('manager.announcements.index', compact('ads','types'));
    }

    /** إنشاء إعلان داخل معهد المدير */
    public function store(Request $request)
    {
        $inst = $this->currentInstitute();

        $data = $request->validate([
            'title'           => 'required|string|max:150',
            'type_id'         => 'required|exists:ads_types,id',
            'description'     => 'nullable|string',
            'link'            => 'nullable|url',
            'end_date'        => 'required|date|after:today',
            'status'          => 'required|in:active,inactive',
            'watches_roles'   => 'required|array',
            'watches_roles.*' => 'in:student,guardian,teacher,admin,manager',
            'image'           => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('ads', 'public');
        }

        $data['user_id'] = Auth::id();
        $data['institute_id'] = $inst->id; // مهم: ربط الإعلان بمعهد المدير

        $ad = Ad::create($data);

        foreach ($data['watches_roles'] as $role) {
            UserAd::create([
                'publish_id'   => Auth::id(),
                'ads_id'       => $ad->id,
                'watches_role' => $role,
            ]);
        }

        return redirect()->route('manager.announcements.index')
            ->with('success','تم إضافة الإعلان بنجاح');
    }

    public function show(Ad $ad)
    {
        $this->ensureScope($ad);
        return view('manager.announcements.show', compact('ad'));
    }

    public function edit(Ad $ad)
    {
        $this->ensureScope($ad);
        $types = AdsType::all();
        return view('manager.announcements.edit', compact('ad','types'));
    }

    public function update(Request $request, Ad $ad)
    {
        $this->ensureScope($ad);

        $data = $request->validate([
            'title'           => 'required|string|max:150',
            'type_id'         => 'required|exists:ads_types,id',
            'description'     => 'nullable|string',
            'link'            => 'nullable|url',
            'end_date'        => 'required|date|after:today',
            'status'          => 'required|in:active,inactive',
            'watches_roles'   => 'required|array',
            'watches_roles.*' => 'in:student,guardian,teacher,admin,manager',

            'image'           => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($ad->image) {
                Storage::disk('public')->delete($ad->image);
            }
            $data['image'] = $request->file('image')->store('ads', 'public');
        }

        // لا نسمح بتغيير institute_id هنا — يبقى بنفس المعهد
        unset($data['institute_id'], $data['user_id']);

        $ad->update($data);

        UserAd::where('ads_id', $ad->id)->delete();
        foreach ($data['watches_roles'] as $role) {
            UserAd::create([
                'publish_id'   => Auth::id(),
                'ads_id'       => $ad->id,
                'watches_role' => $role,
            ]);
        }

        return redirect()->route('manager.announcements.index')
            ->with('success','تم تحديث الإعلان بنجاح');
    }

    public function destroy(Ad $ad)
    {
        $this->ensureScope($ad);

        UserAd::where('ads_id', $ad->id)->delete();
        if ($ad->image) {
            Storage::disk('public')->delete($ad->image);
        }
        $ad->delete();

        return redirect()->route('manager.announcements.index')
            ->with('success','تم حذف الإعلان بنجاح');
    }
}
