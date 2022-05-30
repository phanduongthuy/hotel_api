<?php

namespace App\Http\Requests\Admin\Employee;

use App\Http\Requests\BaseRequest;

class UpdateEmployeeRequest extends BaseRequest
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
            'email' => 'email',
            'phone' => 'numeric|digits:10|regex:/(0)[0-9]{9}/',
            'avatar' => 'file|mimes:jpg,jpeg,png',
        ];
    }

    public function attributes()
    {
        return [
            'email' => 'email',
            'phone' => 'số điện thoại',
            'avatar' => 'Ảnh đại diện'
        ];
    }
}
