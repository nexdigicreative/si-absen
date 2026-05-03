<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:users',
            'role' => 'required|in:admin,guru,siswa,kepala_sekolah',
        ];
    }
}
