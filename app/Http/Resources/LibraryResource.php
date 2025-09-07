<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class LibraryResource extends JsonResource
{
    public function toArray($request)
    {
        $fileMeta = null;

        if ($this->file) {
            $fileMeta = [
                'id'   => $this->file->id,
                'name' => $this->file->name ?? null,
                'size' => $this->file->size ?? null,
                'mime' => $this->file->mime ?? null,
                // 👇 أهم سطر: نبني الرابط عبر asset()
                'url'  => $this->assetFromPath($this->file->path ?? null),
            ];
        }

        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'author'       => $this->author,
            'description'  => $this->description,
            'isbn'         => $this->isbn,
            'category_id'  => $this->category_id,
            'institute_id' => $this->institute_id,
            'is_visible'   => (bool) $this->is_visible,
            'file'         => $fileMeta,
        ];
    }

    /**
     * يبني رابطًا صحيحًا باستخدام asset() لأي مسار محفوظ.
     */
    private function assetFromPath(?string $path): ?string
    {
        if (!$path || !is_string($path)) return null;

        $path = trim($path);

        // لو رابط كامل، أرجعه كما هو
        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        // أزل أي شرطة مائلة في البداية
        $path = ltrim($path, '/');

        // لو المسار يبدأ أصلاً بـ مجلدات عامة، استخدمها كما هي
        if (Str::startsWith($path, ['storage/', 'images/', 'uploads/', 'files/', 'assets/'])) {
            return asset($path);
        }

        // لو يبدأ بـ public/ شيله
        $path = preg_replace('#^public/#i', '', $path);

        // لو يبدأ بـ storage/app/public/ حوّله إلى storage/
        $path = preg_replace('#^storage/app/public/#i', 'storage/', $path);

        // الحالة الافتراضية: اعتبره محفوظًا على disk public (symlink => public/storage)
        return asset('storage/app/public/public/' . $path);
    }
}
