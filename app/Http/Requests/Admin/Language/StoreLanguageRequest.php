<?php

namespace App\Http\Requests\Admin\Language;

use Illuminate\Foundation\Http\FormRequest;

class StoreLanguageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'          => 'required|unique:languages,name',
        ];
    }

    public function messages()
    {
        return [
            'required'        => ':attribute không được để trống',
            'unique'          => ':attribute đã tồn tại',
        ];
    }

    public function attributes()
    {
        return [
            'name'          => 'Ngôn ngữ',
        ];
    }
}
