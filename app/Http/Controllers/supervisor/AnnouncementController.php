<?php

namespace App\Http\Controllers\supervisor;

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
        $this->middleware(['auth','role:admin']);
    }

    /** معهد المشرف الحالي (من قائمة معاهده) */
    protected function currentInstitute(Request $request): Institute
    {
        $user = $request->user();

        $ids = $user->institutes()->pluck('institutes.id'); // معاهد المشرف عبر pivot
        abort_if($ids->isEmpty(), 403, 'لا تملك صلاحية على أي معهد.');

        $picked = (int) $request->query('institute_id', (int) $ids->first());
        abort_if(! $ids->contains($picked), 403, 'هذا المعهد غير مرتبط بحسابك.');

        return Institute::findOrFail($picked);
    }

    /** تحقق الملكية + النطاق (المعهد) */
    protected function ensureScope(Request $request, Ad $ad): void
    {
        $inst = $this->currentInstitute($request);
        abort_if($ad->user_id !== Auth::id(), 403, 'ليس لديك صلاحية على هذا الإعلان.');
        abort_if((int)$ad->institute_id !== (int)$inst->id, 403, 'هذا الإعلان خارج نطاق المعهد الحالي.');
    }

    /** قائمة إعلانات المشرف داخل المعهد الحالي */
    public function index(Request $request)
    {
        $inst = $this->currentInstitute($request);

        $ads = Ad::where('user_id', Auth::id())
            ->where('institute_id', $inst->id)
            ->with('type')
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        $types = AdsType::all();

        return view('supervisor.announcements.index', compact('ads','types'));
    }

    /** إنشاء إعلان داخل المعهد الحالي */
    public function store(Request $request)
    {
        $inst = $this->currentInstitute($request);

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

        $data['user_id']      = Auth::id();
        $data['institute_id'] = $inst->id; // مهم: ربط الإعلان بالمعهد الحالي

        $ad = Ad::create($data);

        foreach ($data['watches_roles'] as $role) {
            UserAd::create([
                'publish_id'   => Auth::id(),
                'ads_id'       => $ad->id,
                'watches_role' => $role,
            ]);
        }

        return redirect()
            ->route('admin.announcements.index', ['institute_id' => $inst->id])
            ->with('success','تم إضافة الإعلان بنجاح');
    }

    public function show(Request $request, Ad $ad)
    {
        $this->ensureScope($request, $ad);
        return view('supervisor.announcements.show', compact('ad'));
    }

    public function edit(Request $request, Ad $ad)
    {
        $this->ensureScope($request, $ad);
        $types = AdsType::all();
        return view('supervisor.announcements.edit', compact('ad','types'));
    }

    public function update(Request $request, Ad $ad)
    {
        $this->ensureScope($request, $ad);

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

        // لا نسمح بتغيير institute_id/user_id من هنا
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

        $inst = $this->currentInstitute($request);
        return redirect()
            ->route('admin.announcements.index', ['institute_id' => $inst->id])
            ->with('success','تم تحديث الإعلان بنجاح');
    }

    public function destroy(Request $request, Ad $ad)
    {
        $this->ensureScope($request, $ad);

        UserAd::where('ads_id', $ad->id)->delete();
        if ($ad->image) {
            Storage::disk('public')->delete($ad->image);
        }
        $ad->delete();

        $inst = $this->currentInstitute($request);
        return redirect()
            ->route('admin.announcements.index', ['institute_id' => $inst->id])
            ->with('success','تم حذف الإعلان بنجاح');
    }
}
