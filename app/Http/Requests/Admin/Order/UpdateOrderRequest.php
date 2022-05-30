<?php

namespace App\Http\Requests\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'order_id'          =>  'required',
            'price_per_page'    =>  'numeric|gte:0',
            'total_page'        =>  'numeric|gte:0',
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
            'order_id'          =>  'đơn hàng',
            'status'            =>  'trạng thái đơn hàng',
            'price_per_page'    =>  'giá tiền một trang',
            'total_page'        =>  'Tổng số trang',
        ];
    }
}
