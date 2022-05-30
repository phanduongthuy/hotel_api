<?php

namespace App\Http\Requests\Admin\PriceList;

use Illuminate\Foundation\Http\FormRequest;

class StorePriceListRequest extends FormRequest
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
            'title' => 'required',
            'image' => 'required',
        ];
    }
    public function attributes()
    {
        return [
            'title' => 'tên tiêu đề',
            'image' => 'bảng giá',
        ];
    }
}
