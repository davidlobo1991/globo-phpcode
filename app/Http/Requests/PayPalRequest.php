<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayPalRequest extends FormRequest
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
            'name' => 'required|min:2',
            'phone' => 'required|min:9|max:16',
            'email' => 'required|email',
            'last_name' => 'required|min:2',
            'country' => 'required|min:4',
            'province' => 'required|min:2',
            'city' => 'required|min:2',
            'post_code' => 'required|min:4',
            'street' => 'required|min:3'
        ];
    }
}
