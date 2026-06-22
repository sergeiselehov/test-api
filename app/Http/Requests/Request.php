<?php

namespace App\Http\Requests;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

abstract class Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'Необходимо заполнить.',
            '*.email' => 'Некорректный Email.',
            '*.max' => 'Максимальное кол-во символов :max.',
            '*.min' => 'Минимальное кол-во символов :min.',
            '*.size' => 'Поле должно содержать :size символа.',
            '*.numeric' => 'Введите числовое значение.',
            '*.integer' => 'Введите целое число.',
            '*.string' => 'Введите текстовое значение.',
            '*.boolean' => 'Введите логическое значение.',
            '*.confirmed' => 'Пароли не совпадают.',
            '*.unique' => 'Значение должно быть уникальным.',
            '*.date' => 'Введите действительную дату.',
            '*.before' => 'Дата должна быть раньше указанной.',
            '*.after' => 'Дата должна быть позже указанной.',
            '*.exists' => 'Выбранное значение недействительно.',
            '*.in' => 'Выбранное значение недопустимо.',
            '*.regex' => 'Неверный формат.',
            '*.array' => 'Значение должно быть массивом.',
            '*.file' => 'Загрузите файл.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            ApiResponse::validationError($validator->errors()->toArray())->toResponse($this)
        );
    }
}
