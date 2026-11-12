<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'nim' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'prodi' => ['nullable', 'string', 'max:255'],
            'institution_name' => ['nullable', 'string', 'max:255'],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'semester' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'target_field' => ['nullable', 'in:it,elektro,all,both'],
            'target_occupation_id' => ['nullable', 'exists:occupations,id'],
            'target_occupation_ids' => ['nullable', 'array'],
            'target_occupation_ids.*' => ['integer', 'exists:occupations,id'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'ipk' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'avatar.uploaded' => 'Foto gagal diunggah karena ukuran file melebihi batas atau koneksi terputus. Sistem telah mengoptimalkan kompresi otomatis untuk foto Anda.',
            'avatar.image' => 'File yang dipilih harus berupa gambar yang valid.',
            'avatar.mimes' => 'Format foto profil harus berupa JPG, JPEG, PNG, atau WEBP.',
            'avatar.max' => 'Ukuran foto tidak boleh melebihi 10MB.',
        ];
    }
}
