<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;


class StudentResource extends JsonResource
{
    public function toArray($request)
    {
        // توليد رابط الصورة إن كنت تستخدم التخزين العام
        $imageUrl = $this->image
            ? (Storage::disk('public')->url($this->image))
            : null;

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
            'image'               => $this->image,
            'image_url'           => $imageUrl,

            // علاقات اختيارية (تُعاد فقط إذا تم تضمينها بـ include)
            'guardian'            => $this->whenLoaded('guardian', function () {
                return [
                    'id'   => $this->guardian->id,
                    'name' => $this->guardian->name, // accessor يتعامل مع first/firstname
                    'user_id' => $this->guardian->user_id,
                ];
            }),
            'user'                => $this->whenLoaded('user', function () {
                return [
                    'id'    => $this->user->id,
                    'email' => $this->user->email,
                    'role'  => $this->user->role->name ?? null,
                ];
            }),
            'classes'             => $this->whenLoaded('classes', function () {
                // عدّل الحقول بما يناسب جدولك
                return $this->classes->map(fn ($c) => [
                    'id'   => $c->id,
                    'name' => $c->name ?? null,
                ]);
            }),
        ];
    }
}
