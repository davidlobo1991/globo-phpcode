<?php

namespace App\Http\Requests;

use App\ReservationType;
use Globobalear\Products\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Globobalear\Payments\Traits\PromocodeSecure;

class ReservationRequest extends FormRequest
{
    use PromocodeSecure;

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
        /**
         * Check if promocode CODE is valid and secure
         */
        $identifier = null;

        switch ($this->reservation_type_id) {
            case ReservationType::PRODUCTS:
                if (isset($this->products[0]["passId"]))
                    $identifier = $this->products[0]["passId"];
                break;
            case ReservationType::PACKS:
                $identifier = $this->products[0]["pack"];
                break;
        }


        $promocode = $this->getVerifiedPromocode($this->promocode, $this->reservation_type_id, $identifier);

        $this->request->add([
            'created_by' => \Auth::user()->id,
            'reservation_number' => uniqid(),
            'promocode_id' => $promocode->id ?? null,
            'discount' => $promocode->discount ?? null,
            'has_passes' => Product::where('id', $this->products)->pluck('has_passes')->first()
        ]);

        //if reservatioo come from pack
        if (isset($this->pack_id)) {
            return [
                "pack_id" => 'required',
                "pack" => 'required',
                "quantity" => 'required|min:0',
                //"pass" => "required|array",
                //"pass.*" => "required",
                //"passpirates" => "required|array",
                //"passpirates.*" => "required",
            ];
        }
        // comes from shows
        if ($this->has_passes) {
            return [
                'passes' => 'required',
                'products' => 'required'
            ];
        }

        return [
            'products' => 'required'
        ];
    }
}
