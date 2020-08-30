<?php

namespace Globobalear\Wristband\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WristbandPassRequest extends FormRequest
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
            'date_start' => 'required|date',
            'date_end' => 'required|date',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'wristband_id' => 'required|numeric'
        ];
    }
}
