<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'book_copy_id' => 'required|exists:book_copies,id',
        ];
    }


    public function messages()
    {
        return [
            'book_copy_id.required' => 'شناسه نسخه کتاب الزامی است.',
            'book_copy_id.exists'   => 'نسخه کتاب انتخاب‌شده وجود ندارد.',
        ];
    }

}
