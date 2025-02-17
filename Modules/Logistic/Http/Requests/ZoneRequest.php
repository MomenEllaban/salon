<?php

namespace Modules\Logistic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZoneRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'logistic_id' => ['required'],
            'country_id' => ['required'],
            'state_id' => ['required'],
            'name' => ['required'],
            'standard_delivery_charge' => ['min:1'],
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
