<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s\-()]+$/'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120']
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Имя обязательно для заполнения',
            'last_name.required' => 'Фамилия обязательна для заполнения',
            'email.required' => 'Email обязателен для заполнения',
            'email.unique' => 'Этот email уже используется',
            'phone.regex' => 'Неверный формат телефона',
            'avatar.image' => 'Аватар должен быть изображением',
            'avatar.mimes' => 'Аватар должен быть в формате JPEG, PNG, JPG или GIF',
            'avatar.max' => 'Размер аватара не должен превышать 2MB',
            'cover_image.image' => 'Обложка должна быть изображением',
            'cover_image.mimes' => 'Обложка должна быть в формате JPEG, PNG, JPG или GIF',
            'cover_image.max' => 'Размер обложки не должен превышать 5MB'
        ];
    }
}
