<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LibraryResource extends JsonResource
{
    public function toArray($request)
    {
        $fileUrl = null;
        $fileMeta = null;

        if ($this->file) {
            // نفترض أن عمود path محفوظ على disk public
            $fileUrl = $this->file->path ? Storage::disk('public')->url($this->file->path) : null;

            $fileMeta = [
                'id'   => $this->file->id,
                'name' => $this->file->name ?? null,
                'size' => $this->file->size ?? null,
                'mime' => $this->file->mime ?? null,
                'url'  => $fileUrl,
            ];
        }

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'author'      => $this->author,
            'description' => $this->description,
            'isbn'        => $this->isbn,
            'category_id' => $this->category_id,
            'institute_id'=> $this->institute_id,
            'is_visible'  => (bool) $this->is_visible, // للمراجعة فقط
            'file'        => $fileMeta,
        ];
    }
}
