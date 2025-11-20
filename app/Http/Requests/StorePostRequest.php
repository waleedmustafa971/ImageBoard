<?php

namespace App\Http\Requests;

use App\Helpers\Captcha;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Anonymous posting allowed
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:100'],
            'content' => ['required', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'], // 5MB (legacy single image)
            'images' => ['nullable', 'array', 'max:4'], // Up to 4 images
            'images.*' => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'], // 5MB each
            'captcha' => ['required', 'numeric'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!Captcha::validate($this->captcha)) {
                $validator->errors()->add('captcha', 'Incorrect captcha answer. Please try again.');
            }
        });
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->name ?: 'Anonymous',
        ]);
    }
}
