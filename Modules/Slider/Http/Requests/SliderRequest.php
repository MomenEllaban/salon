<?php

namespace Modules\Slider\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class sliderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch (strtolower($this->getMethod())) {
            case 'post':
                return [
                    'name' => 'required|string|max:255',
                    'link' => 'nullable|string',
                    'type' => 'required|string',
                ];
                break;
            case 'put':
            case 'patch':
                return [
                    'name' => 'required|string|max:255',
                    'link' => 'nullable|string',
                    'type' => 'required|string',
                ];
                break;

            default:
                // code...
                break;
        }
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
