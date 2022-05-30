<?php

namespace App\Http\Requests\Admin\Verify;

use App\Http\Requests\BaseRequest;

class VerifyEmailRequest extends BaseRequest
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
            'email' => 'required|email',
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
            'email' => 'Email',
        ];
    }

    public function messages()
    {
        return [
            'email.email'       => 'Email không hợp lệ',
            'email.required'    => 'Email không được để trống'
        ];
    }
}
