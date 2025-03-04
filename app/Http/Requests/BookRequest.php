<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'title.required'     => 'عنوان کتاب الزامی است.',
            'title.string'       => 'عنوان کتاب باید یک رشته باشد.',
            'title.max'          => 'عنوان کتاب نمی‌تواند بیش از 255 کاراکتر باشد.',
            'author.required'    => 'نام نویسنده الزامی است.',
            'author.string'      => 'نام نویسنده باید یک رشته باشد.',
            'author.max'         => 'نام نویسنده نمی‌تواند بیش از 255 کاراکتر باشد.',
            'description.string' => 'توضیحات باید یک رشته باشد.',
            'description.max'    => 'توضیحات نمی‌تواند بیش از 1000 کاراکتر باشد.',
        ];
    }

}
