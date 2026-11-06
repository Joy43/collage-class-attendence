<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicSessionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'between:2000,2100'],
            'term' => [
                'required', 'string', 'max:50',
                Rule::unique('academic_sessions')
                    ->where('year', $this->input('year'))
                    ->ignore($this->route('session')),
            ],
        ];
    }
}
