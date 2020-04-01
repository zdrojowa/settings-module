<?php

namespace Selene\Modules\SettingsModule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255|string',
            'key' => 'required|max:255|string|unique:mongodb.settings,'.$this->setting->id,
            'value' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'To pole jest wymagane.',
            'name.max' => 'Nazwa może mieć maksymalnie 255 znaków.',
            'name.string' => 'Nazwa jest nieprawidłowa',
            'key.required' => 'To pole jest wymagane.',
            'key.max' => 'Klucz może mieć maksymalnie 255 znaków.',
            'key.string' => 'Klucz jest nieprawidłowy.',
            'key.unique' => 'Klucz musi być unikalny.',
            'value.required' => 'To pole jest wymagane.'
        ];
    }
}
