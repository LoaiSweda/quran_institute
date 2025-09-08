<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str; // ⬅️ مهم

class StudentResource extends JsonResource
{
    public function toArray($request)
    {
        // بناء رابط الصورة باستخدام asset('storage/...'):
        $imageUrl = null;
        if (!empty($this->image)) {
            $path = (string) $this->image;

            // إذا الحقل يحتوي رابطًا جاهزًا أو data URI نُعيده كما هو
            if (Str::startsWith($path, ['http://', 'https://', 'data:'])) {
                $imageUrl = $path;
            } else {
                // لو كان المسار محفوظًا كبادئة public/ نحذفها
                $path = preg_replace('#^/?public/#', '', $path);
                // نبني رابطًا صالحًا عبر public/storage
                $imageUrl = asset('storage/app/public/' . ltrim($path, '/'));
            }
        }

        return [
            'id'                  => $this->id,
            'first_name'          => $this->first_name,
            'last_name'           => $this->last_name,
            'full_name'           => trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')),
            'phone'               => $this->phone,
            'address'             => $this->address,
            'birthdate'           => optional($this->birthdate)->format('Y-m-d'),
            'father_name'         => $this->father_name,
            'points'              => $this->points,
            'present_percentage'  => $this->present_percentage,
            'qr'                  => $this->qr,

            // الصورة
            'image'               => $this->image,   // القيمة كما في قاعدة البيانات
            'image_url'           => $imageUrl,      // رابط جاهز للواجهة

            // علاقات اختيارية
            'guardian' => $this->whenLoaded('guardian', function () {
                return [
                    'id'      => $this->guardian->id,
                    'name'    => $this->guardian->name,
                    'user_id' => $this->guardian->user_id,
                ];
            }),

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'    => $this->user->id,
                    'email' => $this->user->email,
                    'role'  => $this->user->role->name ?? null,
                ];
            }),

            'classes' => $this->whenLoaded('classes', function () {
                return $this->classes->map(fn ($c) => [
                    'id'   => $c->id,
                    'name' => $c->name ?? null,
                ]);
            }),
        ];
    }
}
