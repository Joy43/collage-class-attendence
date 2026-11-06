<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('courses')->ignore($this->route('course'))],
            'title' => ['required', 'string', 'max:100'],
            'credit' => ['required', 'integer', 'between:1,10'],
        ];
    }
}
