<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FacultyRequest extends FormRequest
{
    public function rules(): array
    {
        $editing = $this->route('faculty') !== null;

        return [
            'name' => ['required', 'string', 'max:100'],
            'user_name' => ['required', 'string', 'max:50', Rule::unique('faculties')->ignore($this->route('faculty'))],
            // Required when creating; optional when editing (blank = keep current).
            'password' => [$editing ? 'nullable' : 'required', 'string', 'min:6'],
        ];
    }
}
