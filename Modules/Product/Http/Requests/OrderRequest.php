<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'shipping_address_id' => ['required'],
            'phone' => ['required'],
            'chosen_logistic_zone_id' => ['required'],
            'shipping_delivery_type' => ['required'],
            'payment_method' => ['required'],
            'payment_status' => ['required'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
