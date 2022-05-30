<?php

namespace App\Http\Requests\Admin\Member;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
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
//            'email' => 'required|email',
//            'phone' => 'numeric|digits:10|regex:/(0)[0-9]{9}/',
//            'image' => 'file|mimes:jpg,jpeg,png',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'tên thành viên',
            'email' => 'email',
            'phone' => 'số điện thoại',
            'address' => 'Địa chỉ',
            'image' => 'Ảnh đại diện'
        ];
    }
}
