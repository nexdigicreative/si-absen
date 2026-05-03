<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $id = $user ? $user->id : null;

        return [
            'name' => 'required|string|max:100',
            'email' => "nullable|email|unique:users,email,{$id}",
            'role' => 'required|in:admin,guru,siswa,kepala_sekolah',
        ];
    }
}
