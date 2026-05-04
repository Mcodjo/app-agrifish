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
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'preferences' => ['nullable', 'array'],
            'preferences.service_updates' => ['sometimes', 'boolean'],
            'preferences.status_notifications' => ['sometimes', 'boolean'],
            'preferences.newsletter' => ['sometimes', 'boolean'],
            'preferences.preferred_contact' => ['nullable', 'in:email,phone,whatsapp'],
        ];
    }
}
