<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        $imageUrl = $this->image ? Storage::disk('public')->url($this->image) : null;

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'image_url'   => $imageUrl,
            'items_count' => $this->when(isset($this->items_count), $this->items_count),
        ];
    }
}
