<?php

namespace App\Http\Requests\Admin\User;

use App\Http\Requests\BaseRequest;

class ChangePasswordRequest extends BaseRequest
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
            'current_password' => 'required|min:6',
            'password' => 'required|min:6|confirmed',
        ];
    }

    public function attributes()
    {
        return [
            'current_password' => 'Mật khẩu hiện tại',
            'password' => 'Mật khẩu',
        ];
    }

    public function messages(): array
    {
        return [
            'required'              => ':attribute không được để trống',
            'password.confirmed'          => ':attribute xác nhận không chính xác',
        ];
    }
}
