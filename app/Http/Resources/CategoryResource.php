<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            // 👇 نبني رابط الصورة عبر asset() بنفس الدالة
            'image_url'   => $this->assetFromPath($this->image ?? null),
            'items_count' => $this->when(isset($this->items_count), $this->items_count),
        ];
    }

    private function assetFromPath(?string $path): ?string
    {
        if (!$path || !is_string($path)) return null;

        $path = trim($path);

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (Str::startsWith($path, ['storage/', 'images/', 'uploads/', 'files/', 'assets/'])) {
            return asset($path);
        }

        $path = preg_replace('#^public/#i', '', $path);
        $path = preg_replace('#^storage/app/public/#i', 'storage/', $path);

        return asset('storage/app/public/public/' . $path);
    }
}
