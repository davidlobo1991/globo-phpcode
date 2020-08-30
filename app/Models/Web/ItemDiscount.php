<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class ItemDiscount
{
    public $id;
    public $item, $_item, $product, $originalProduct, $promocode, $discount_applied;
    public $realPassId, $crsCat;

    public function __construct(array $array)
    {
        $this->id = $array['id'] ?? uniqid();
        $this->_item = json_encode(collect($array['item']));
        $this->item = $array['item'];
        $this->product = $array['product'];
        $this->originalProduct = $array['originalProduct'];
        $this->promocode = $array['promocode_id'];
        $this->discount_applied = $array['discount_applied'];
        $this->crsCat = $array['crsCat'];
        $this->realPassId = $array['realPassId'];

    }
}
