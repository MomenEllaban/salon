<?php

namespace Modules\Promotion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromotionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'use_limit' => ['required', 'integer', 'min:1'],
        ];

        // Conditionally add unique rule for coupon_code based on coupon_type
        if ($this->input('coupon_type') === 'custom') {
            $rules['coupon_code'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('promotions_coupon', 'coupon_code')->ignore($this->route('promotion')), // Ignore current promotion ID if updating
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'coupon_code.unique' => 'coupon code must be unique.',
            'use_limit.min' => 'Use limit must be greater than or equal to 1'];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
