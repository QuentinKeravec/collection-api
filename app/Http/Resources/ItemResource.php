<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'type'=>$this->type,
            'year'=>$this->year,
            'author'=>$this->author,
            'description'=>$this->description,
            'tags'=>$this->tags->pluck('name'),
            'image_url' => $this->image_path ? asset('storage/' . $this->image_path) : null,
            'is_favorite' => $user ? $user->favorites->contains($this->id) : false,
            'created_at'=>$this->created_at,
        ];
    }
}
