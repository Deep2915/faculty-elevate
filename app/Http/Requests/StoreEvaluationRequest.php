<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'faculty_id' => ['required', 'string'],
            'period' => ['required', 'string', 'max:50'],
            'scores.research' => ['required', 'numeric', 'between:0,1'],
            'scores.teaching' => ['required', 'numeric', 'between:0,1'],
            'scores.innovation' => ['required', 'numeric', 'between:0,1'],
            'scores.student_clarity' => ['nullable', 'numeric', 'between:0,1'],
            'scores.attendance' => ['nullable', 'numeric', 'between:0,1'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:draft,published'],
        ];
    }
}
