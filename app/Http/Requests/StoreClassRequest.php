<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'grade' => 'required|integer|in:10,11,12',
            'major' => 'nullable|string|max:50',
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'room' => 'nullable|string|max:20',
            'max_students' => 'integer|min:1|max:50',
        ];
    }
}
