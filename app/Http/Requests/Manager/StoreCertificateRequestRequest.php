<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificateRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('institute manager') === true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required','integer','exists:students,id'],
            'subject_id' => ['required','integer','exists:subjects,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'يجب اختيار الطالب.',
            'student_id.exists'   => 'الطالب غير موجود.',
            'subject_id.required' => 'يجب اختيار المادة.',
            'subject_id.exists'   => 'المادة غير موجودة.',
        ];
    }
}
