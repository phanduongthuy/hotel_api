<?php

namespace App\Http\Requests\User\Feedback;

use App\Http\Requests\BaseRequest;

class StoreFeedbackRequest extends BaseRequest
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
            'order_id'  => 'required',
            'rate_star' => 'required|numeric',
            'content'   => 'required',
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
            'order_id'  => 'Đơn hàng',
            'rate_star' => 'Sao',
            'content'   => 'Nội dung đánh giá',
        ];
    }
}
