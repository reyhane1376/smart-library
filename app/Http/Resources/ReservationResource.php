<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'book_copy_id'   => $this->book_copy_id,
            'book_title'     => $this->whenLoaded('bookCopy', fn() => $this->bookCopy->book->title),
            'reserved_at'    => $this->reserved_at->toDateTimeString(),
            'expires_at'     => $this->expires_at ? $this->expires_at->toDateTimeString() : null,
            'status'         => $this->status,
            'queue_position' => $this->queue_position,
            'created_at'     => $this->created_at->toDateTimeString(),
            'updated_at'     => $this->updated_at->toDateTimeString(),
        ];
    }
}
