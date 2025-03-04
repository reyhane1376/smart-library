<?php

namespace App\Http\Resources;

use App\Models\BookCopy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'author'           => $this->author,
            'description'      => $this->description,
            'available_copies' => $this->whenLoaded('copies', fn() => $this->copies->where('status', BookCopy::STATUS_AVAILABLE)->count()),
            'created_at'       => $this->created_at->toDateTimeString(),
            'updated_at'       => $this->updated_at->toDateTimeString(),
        ];

    }
}
