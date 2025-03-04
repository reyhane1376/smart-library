<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowingResource extends JsonResource
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
            'user_id'          => $this->user_id,
            'book_copy_id'     => $this->book_copy_id,
            'book_title'       => $this->whenLoaded('bookCopy', fn() => $this->bookCopy->book->title),
            'borrowed_at'      => $this->borrowed_at->toDateString(),
            'due_date'         => $this->due_date->toDateString(),
            'returned_at'      => $this->returned_at ? $this->returned_at->toDateString() : null,
            'return_condition' => $this->return_condition,
            'delay_days'       => $this->delay_days,
            'fine_amount'      => $this->fine_amount,
            'created_at'       => $this->created_at->toDateTimeString(),
            'updated_at'       => $this->updated_at->toDateTimeString(),
        ];

    }
}
