<?php

namespace App\Http\Requests\Admin\Footer;

use Illuminate\Foundation\Http\FormRequest;

class StoreFooterInfoRequest extends FormRequest
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
            'company'               => 'required',
            'legal_representation'  => 'required',
            'email'                 => 'required',
            'phone'                 => 'required',
            'address'               => 'required',
            'business_license'      => 'required',
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
            'company' => 'Tên công ty',
            'legal_representation'  => 'Tên người đại diện pháp lý',
            'email'                 => 'email',
            'phone'                 => 'Số điện thoại',
            'address'               => 'Địa chỉ',
            'business_license'      => 'Giấy phép kinh doanh',
        ];
    }
}
