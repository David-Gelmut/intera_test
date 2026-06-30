<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
       // return auth()->check();
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => [
                'required',
                'string',
                // Пример: https://yandex.ru/maps/org/new_star/1095444416 или https://yandex.com/maps/org/new_star/1095444416
                'regex:/^https:\/\/yandex\.(ru|com)\/maps\/org\/([^\/]+\/)?\d+/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'Поле со ссылкой обязательно для заполнения.',
            'url.string' => 'Ссылка должна быть текстовой строкой.',
            'url.regex' => 'Предоставленная ссылка не является валидной ссылкой на организацию в Яндекс.Картах.',
        ];
    }
}
