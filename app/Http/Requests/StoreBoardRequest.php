<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBoardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $boardId = $this->route('board')?->id;

        return [
            'slug' => [
                'required',
                'string',
                'max:10',
                'alpha_dash',
                Rule::unique('boards')->ignore($boardId),
            ],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'is_nsfw' => ['boolean'],
        ];
    }
}
