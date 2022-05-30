<?php

namespace App\Http\Requests\Admin\General;

use Illuminate\Foundation\Http\FormRequest;

class StoreGeneralPloblemRequest extends FormRequest
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
            'title'     => 'required',
            'content'   => 'required',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'title'     => 'Tiêu đề',
            'content'   => 'Nội dung',
        ];
    }
}
