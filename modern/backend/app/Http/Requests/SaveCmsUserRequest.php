<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SaveCmsUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('access.manage');
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->email)) {
            $this->merge(['email' => strtolower(trim($this->email))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'is_active' => ['required', 'boolean'],
            'password' => [$this->isMethod('post') ? 'required' : 'nullable', 'string', 'max:72', Password::min(8)->mixedCase()->numbers()->symbols()],
            'is_system' => ['prohibited'], 'permissions' => ['prohibited'], 'auth_version' => ['prohibited'], 'must_change_password' => ['prohibited'],
        ];
    }
}
