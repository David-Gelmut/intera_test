<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;


class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::defaults()],
            'password_confirmation' => ['required', 'string', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Пожалуйста, введите ваше имя.',
            'email.required' => 'Электронная почта обязательна для заполнения.',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'email.unique' => 'Пользователь с таким Email уже зарегистрирован в системе.',
            'password.required' => 'Придумайте и введите пароль.',
            'password_confirmation.confirmed' => 'Введенные пароли не совпадают.',
            'password_confirmation.required' => 'Пожалуйста, повторите введенный пароль.',
        ];
    }
}
