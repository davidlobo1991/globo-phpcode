<?php
/**
 * Created by PhpStorm.
 * User: moussa
 * Date: 28/11/17
 * Time: 14:32
 */


namespace Globobalear\Promocodes\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromocodeRequest extends FormRequest
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
            'promocode' => 'required',
        ];
    }
}
