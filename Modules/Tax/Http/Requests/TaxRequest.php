<?php

namespace Modules\Tax\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class taxRequest extends FormRequest
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
                    'title' => 'required|string',
                    'type' => 'required|string',
                    'value' => 'required',
                ];
                break;
            case 'put':
            case 'patch':
                $branchId = $this->route('branch');

                return [
                    'title' => 'required|string',
                    'type' => 'required|string',
                    'value' => 'required',
                ];
                break;
            default:
                return [];
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
