<?php

namespace App\Http\Requests\Admin\Bank;

use App\Http\Requests\BaseRequest;

class UpdateBankRequest extends BaseRequest
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
            'account_number' => 'required',
            'bank_name' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Tên chủ tài khoản',
            'account_number' => 'Số tài khoản',
            'bank_name' => 'Tên ngân hàng'
        ];
    }
}
