<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'roll_no' => ['required', 'string', 'max:20', Rule::unique('students')->ignore($this->route('student'))],
            'name' => ['required', 'string', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['roll_no' => trim((string) $this->roll_no), 'name' => trim((string) $this->name)]);
    }
}
