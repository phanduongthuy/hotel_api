<?php

namespace App\Http\Requests\Admin\Employee;

use App\Http\Requests\BaseRequest;

class StoreEmployeeRequest extends BaseRequest
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
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric|digits:10|regex:/(0)[0-9]{9}/',
            'avatar' => 'file|mimes:jpg,jpeg,png',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'tên nhân viên',
            'email' => 'email',
            'phone' => 'số điện thoại',
            'address' => 'Địa chỉ',
            'avatar' => 'Ảnh đại diện'
        ];
    }
}
