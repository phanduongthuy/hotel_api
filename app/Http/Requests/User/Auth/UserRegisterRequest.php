<?php

namespace App\Http\Requests\User\Auth;

use App\Http\Requests\BaseRequest;

class UserRegisterRequest extends BaseRequest
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
            'user_name' => 'required|unique:users,user_name',
            'password'  => 'required|min:6|confirmed',
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
            'user_name' => 'Tên đăng nhập',
            'password'  => 'Mật khẩu',
        ];
    }
}
