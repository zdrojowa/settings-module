<?php

namespace Selene\Modules\SettingsModule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type'  => 'required|max:255|string',
            'key'   => 'required|max:255|string|unique:mongodb.settings',
            'value' => 'required|max:255|string'
        ];
    }

    public function messages()
    {
        return [
            '*.string'   => 'To pole wymaga podania wartosci alfanumerycznej.',
            '*.max'      => 'To pole może zawierać maksymalnie 250 znaków.',
            '*.unique'   => 'To pole musi być unikalne.',
            '*.required' => 'To pole jest wymagane.',
        ];
    }
}
