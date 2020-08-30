<?php

namespace App\Models\Web;

use Illuminate\Http\Request;

use Illuminate\Support\Collection;

use Illuminate\Database\Eloquent\Model;

use App\GlobalConf;
use App\ReservationType;

use PayPal\Api\Item;

use Globobalear\Customers\Models\Customer;

use Globobalear\Packs\Models\Pack;

use Globobalear\Products\Models\Pass;
use Globobalear\Products\Models\SeatType;
use Globobalear\Products\Models\TicketType;
use Globobalear\Products\Models\PassPirates;

use Globobalear\Promocodes\Models\Promocode;

use Globobalear\Wristband\Models\WristbandPass;

use Globobalear\Payments\Traits\PromocodeSecure;

use Globobalear\Products\Models\Product;

use Log;
use Exception;

use Carbon\Carbon;

class Cart
{
    use PromocodeSecure;

    const CART_KEY_ = 'CART_GLOBO_';
    const CURRENCY = 'EUR';
    const CURRENCY_SIM = '€';
    const ABR_PRODUCT = 'PRO-';
    const ABR_PACK = 'PAC-';
    const ABR_SHOW = 'SHW-';
    const ABR_WRISTBAND = 'WRB-';

    public $products;
    public $shows;
    public $packs;
    public $wristbandPasses;

    private $discount;
    private $items;
    private $itemsWithDiscount;
    private $bookinFee;
    private $paypalTax;
    private $form;
    private $promocode = '';
    private $referenceNumber;
    private $paymentMethod;

    /**
     * Cart constructor.
     *
     * @param array          $items     cart items
     * @param String or null $promocode promcoode
     *
     * @return void
     */
    public function __construct(array $items = [], String $promocode = "")
    {
        $this->products = collect();
        $this->shows = collect();
        $this->packs = collect();

        $this->promocode = '';

        $this->bookinFee = (int) GlobalConf::first()->booking_fee ?? 0;
        $this->paypalTax = (int) GlobalConf::first()->paypal ?? 0;
    }

    /**
     * Get the products
     *
     * @return array
     */
    private function getProducts() : Collection
    {
        return $this->products;
    }

    /**
     * Get the pirates shows
     *
     * @return array
     */
    private function getShows() : Collection
    {
        return $this->shows;
    }

    /**
     * Get the packs
     *
     * @return array
     */
    private function getPacks() : Collection
    {
        return $this->packs;
    }

    /**
     * Sets the reference number
     *
     * @param String $referenceNumber reference number to set
     *
     * @return void
     */
    public function setReferenceNumber(String $referenceNumber) : void
    {
        $this->referenceNumber = $referenceNumber;
    }

    /**
     * Gets the reference number
     *
     * @return String
     */
    public function getReferenceNumber() : String
    {
        return $this->referenceNumber;
    }

    /**
     * Sets the payment mehod
     *
     * @param String $paymentMethod the payment method to set
     *
     * @return void
     */
    public function setPaymentMethod(String $paymentMethod) : void
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * Gets the payment method
     *
     * @return String
     */
    public function getPaymentMethod() : String
    {
        return $this->paymentMethod;
    }

    /**
     * Sets the products
     *
     * @param array $products products to set
     *
     * @return void
     */
    public function setProducts(array $products) : void
    {
        $this->products = collect();
        $items = [];

        foreach ($products as $product) {
            $productObject = Product::find($product['productId']);

            $quantity = $product['quantity'] ?? 1;
            $productName = $product['productName'];
            $prpductId = $product['productId'];
            $priceObj = $productObject->prices->where('seat_type_id', $product['seatTypeId'])->where('ticket_type_id', $product['ticketTypeId'])->first();
            $price = 0.090;

            if ($priceObj) {
                $price = (float) $priceObj->price;
            }

            if ($productObject->has_passes) {
                $passGlobo = Pass::find($product['passId']);

                //Si el pase no existe!
                if (!$passGlobo) {
                    return;
                }

                $passSeatType = $passGlobo->passes_seat_types->where('seat_type_id', $product['seatTypeId'])->first();

                $passSeatTypePrices = $passSeatType->passes_prices->where('ticket_type_id', $product['ticketTypeId'])->first();

                $price = (float) $passSeatTypePrices->price;
                $productName = $passGlobo->title;
                $productId = $passGlobo->id;
            }

            $priceItem = ($price * (int) $quantity);

            $item = $this->formatArrayProductToObject($product);

            $paypalItem = new Item();
            $paypalItem->setName($productName)
                ->setCurrency(self::CURRENCY)
                ->setQuantity($quantity)
                ->setSku(self::ABR_PRODUCT .$prpductId)
                ->setPrice($priceItem);

            $item->paypalItem = $paypalItem;

            $items[] = $item;
        }

        $this->products = collect($items);
    }

    public function formatArrayProductToObject($productArray)
    {
        $item = new \stdClass;
        $item->realPassId = (int) $productArray['realPassId'];
        $item->seatTypeId = (int) $productArray['seatTypeId'];
        $item->ticketTypeId = $productArray['ticketTypeId'];
        $item->passId = (int) $productArray['passId'];
        $item->crsCat = $productArray['crsCat'];
        $item->seatTypeName = $productArray['seatTypeName'];

        if ($item->realPassId) {
            $item->passDate = Carbon::createFromFormat('Y-m-d H:i', $productArray['passDate']);
        }

        $item->price = (float) $productArray['price'];
        $item->quantity = (int) $productArray['quantity'] ?? 1;
        $item->image = $productArray['image'];
        $item->showName = $productArray['productName'];
        $item->showId = $productArray['productId'];
        $item->hasDiscount = $productArray['hasDiscount'];

        return $item;
    }

    /**
     * Sets the shows
     *
     * @param array $shows shows to set
     *
     * @return void
     */
    public function setShows(array $shows) : void
    {
        $this->shows = collect();
        $items = [];

        foreach ($shows as $show) {
            $passPirates = PassPirates::find($show['passId']);

            //Si el pase no existe!
            if (!$passPirates) {
                return;
            }

            $showPrice = $show['price'];
            $passPirates->getPriceByParams($show['ticketTypeId'], $show['seatTypeId']);

            $quantity = $show['quantity'] ?? 1;

            $item = $this->formatArrayShowToObject($show);

            $paypalItem = new Item();
            $paypalItem->setName($passPirates->title)
                ->setCurrency(self::CURRENCY)
                ->setQuantity($quantity)
                ->setSku(self::ABR_SHOW.$passPirates->id)
                ->setPrice($showPrice);

            $item->paypalItem = $paypalItem;

            $items[] = $item;
        }

        $this->shows = collect($items);
    }

    public function formatArrayShowToObject($showArray)
    {
        $item = new \stdClass;
        $item->realPassId = (int) $showArray['realPassId'];
        $item->seatTypeId = (int) $showArray['seatTypeId'];
        $item->ticketTypeId = (int) $showArray['ticketTypeId'];
        $item->passId = (int) $showArray['passId'];
        $item->crsCat = $showArray['crsCat'];
        $item->seatTypeName = $showArray['seatTypeName'];
        $item->passDate = Carbon::createFromFormat('Y-m-d H:i', $showArray['passDate']);
        $item->price = (float) $showArray['price'];
        $item->quantity = (int) $showArray['quantity'];
        $item->image = $showArray['image'];
        $item->showName = $showArray['productName'];
        $item->showId = $showArray['productId'];
        $item->hasDiscount = $showArray['hasDiscount'];

        return $item;
    }

    /**
     * Sets the packs
     *
     * @param array $packs packs to set
     *
     * @return void
     */
    public function setPacks(array $packs) : void
    {
        $this->packs = collect();
        $items = [];

        foreach ($packs as $pack) {
            $quantity = $pack['quantity'] ?? 1;

            $item = new \stdClass;
            $item->packId = $pack['packId'];
            $item->price = $pack['price'];
            $item->quantity = (int) $pack['quantity'];
            $item->image = $pack['image'];
            $item->name = $pack['name'];
            $item->wristbands = $pack['wristbands'];
            $item->crsCat = $pack['crsCat'];
            $shows = [];
            foreach ($pack['shows'] ?? [] as $show) {
                $shows[] = $this->formatArrayShowToObject($show);
            }
            $item->shows = collect($shows);
            $products = [];
            foreach ($pack['products'] ?? [] as $product) {
                $products[] = $this->formatArrayProductToObject($product);
            }
            $item->products = collect($products);

            $paypalItem = new Item();
            $paypalItem->setName($pack['name'])
                ->setCurrency(self::CURRENCY)
                ->setQuantity($quantity)
                ->setSku(self::ABR_PACK.$pack['packId'])
                ->setPrice($pack['price']);

            $item->paypalItem = $paypalItem;

            $items[] = $item;
        }

        $this->packs = collect($items);
    }

    /**
     * Sets the wristband passes
     *
     * @param array $wristbandPasses wristband passes to set
     *
     * @return void
     */
    // public function setWristbandPasses(array $wristbandPasses) : void
    // {
    //     $this->wristbandPasses = $wristbandPasses;
    // }

    /**
     * Sets the promocode
     *
     * @param String $promocode promocode to set
     *
     * @return void
     */
    public function setPromoCode(String $promocode) : void
    {
        $this->promocode = $promocode;
    }

    /**
     * Sets the form
     *
     * @param \Illuminate\Http\Request $request the request
     *
     * @return void
     */
    public function setForm(Request $request) : void
    {
        $this->form = $request->only(
            [
                'name',
                'last_name',
                'phone',
                'email',
                'comments',
                'payment_option'
            ]
        );

        $name = $request->name;
        $lastName = $request->last_name;

        $request->merge(['name' => $name . ' ' . $lastName]);

        $customer = null;

        try{
            $customer = Customer::where('email', $request->email)->first() ??  Customer::create($request->all());

            $this->form['customer_id'] = $customer->id;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            $this->form['customer_id'] = null;
        }
    }

    /**
     * Gets the subtotal
     *
     * @return float
     */
    public function getSubtotal() : float
    {
        $subtotal = 0;

        foreach ($this->products as $product) {
            $subtotal += $product->quantity * $product->price;
        }
        foreach ($this->shows as $show) {
            $subtotal += $show->quantity * $show->price;
        }
        foreach ($this->packs as $pack) {
            $subtotal += $pack->quantity * $pack->price;
        }

        return ($subtotal - $this->getDiscount() + $this->getBookinFee());
    }

    /**
     * Gets the discount
     *
     * @return float
     */
    public function getDiscount() : float
    {
        return $this->discount ?? 0.00;
    }

    /**
     * Gets the total
     *
     * @return float
     */
    public function getTotal() : float
    {
        return $this->getSubtotal() + $this->getPaypalTax();
    }

    /**
     * Gets the promocode
     *
     * @return string
     */
    public function getPromocode() : String
    {
        return $this->promocode;
    }

    /**
     * Gets the form
     *
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    public function getFormAttribute($attribute = '')
    {
        return $this->form[$attribute] ?? null;
    }

    /**
     * Gets the items with discount
     *
     * @return array
     */
    public function getItemsWithDiscount() : array
    {
        return $this->itemsWithDiscount ?? [];
    }

    /**
     * Process the promocode
     *
     * @param Request $request the request
     *
     * @return array
     */
    public function processPromocode(string $promocodeCode) : ?Promocode
    {
        foreach ($this->products as $product) {
            $promocode = $this->getVerifiedPromocode($promocodeCode, ReservationType::PRODUCTS, $product);
            if ($promocode) {
                $pass = Pass::find((int) $product->passId);

                $this->applyPromocode($promocode, $product, $pass);

                $this->promocode = $promocodeCode;

                return $promocode;
            }
        }

        foreach ($this->packs as $pack) {
            $promocode = $this->getVerifiedPromocode($promocodeCode, ReservationType::PACKS, $pack);
            if ($promocode) {
                $packToFind = Pack::find((int) $pack->packId);

                $this->applyPromocode($promocode, $pack, $packToFind);

                $this->promocode = $promocodeCode;

                return $promocode;
            }
        }

        return null;
    }

    /**
     * Gets the booking fee
     *
     * @return float
     */
    public function getBookinFee() : float
    {
        return $this->bookinFee;
    }

    /**
     * Retorno de $ a añadir al total
     *
     * @return float
     */
    public function getPaypalTax() : float
    {
        //porcentaje aplicado al subtotal
        $tax = ($this->paypalTax / 100) * $this->getSubtotal();

        return $tax;
    }

    /**
     * Gets the paypal items
     *
     * @return array
     */
    public function getPayPalItems() : Collection
    {
        $items = $this->getProducts()->pluck('paypalItem');
        $items = $items->merge($this->getShows()->pluck('paypalItem'));
        $items = $items->merge($this->getPacks()->pluck('paypalItem'));
        $items = $items->merge($this->getPaypalTaxesItem());

        $items = $items->merge(collect($this->itemsWithDiscount)->pluck('paypalItem'));

        return $items;
    }

    /**
     * Cast the taxes
     *
     * @return array
     */
    private function getPaypalTaxesItem() : Collection
    {
        $paypalTaxItem = new Item();
        $bookingFeeItem = new Item();

        $paypalTaxItem->setName('PayPal commission')
            ->setCurrency(self::CURRENCY)
            ->setQuantity(1)
            ->setSku('TAX-PAYPAL')
            ->setPrice($this->getPaypalTax());

        $bookingFeeItem->setName('Booking fee commission')
            ->setCurrency(self::CURRENCY)
            ->setQuantity(1)
            ->setSku('TAX-BOOKINGFEE')
            ->setPrice($this->getBookinFee());

        return collect([
            $paypalTaxItem,
            $bookingFeeItem
        ]);
    }

    /**
     * Apply discount for cart and return discount for product
     *
     * @param Promocode $promocode the promocode to appy
     * @param float     $priceItem the item price
     * @param array     $item      the item
     * @param Pass      $pass      the pass
     * @param mixed     $product   the product
     *
     * @return float
     */
    // private function applyPromocode(Promocode $promocode, float $priceItem = 0.00, array $item = [], ? Pass $pass = null, $product = []) : float
    private function applyPromocode(Promocode $promocode, $product, $pass) : void
    {
        $discount = $promocode->calculateDiscountByPrice($product->price * $product->quantity);

        $this->discount += $discount;
        $itemDiscount = new ItemDiscount(
            [
                'id' => $product->realPassId ?? 'PCK-' . $product->packId,
                'item' => [], // No se porque esta este campo, estoy haciendo limpieza i siempre se pasasba por parametro una array vacia
                'product' => $product,
                'originalProduct' => $pass,
                'promocode_id' => $promocode->id,
                'discount_applied' => $discount,
                'crsCat' => $product->crsCat ?? new \stdClass(),
                'realPassId' => $product->realPassId ?? $product->packId
            ]
        );

        $paypalItem = new Item();
        $paypalItem->setName('Promocode '.$promocode->code)
            ->setCurrency(self::CURRENCY)
            ->setQuantity(1)
            ->setSku($itemDiscount->id)
            ->setPrice($itemDiscount->discount_applied * -1);

        $itemDiscount->paypalItem = $paypalItem;
        $this->itemsWithDiscount[] = $itemDiscount;
        // return $product->price - (($product->price * $discount) / 100);
    }

    /**
     * Finds the discount by the item id
     *
     * @param int     $id       the item id
     * @param boolean $getValue will get the value?
     *
     * @return mixed
     */
    public function finfDiscountItemById(int $id, bool $getValue = false) // : ? mixed
    {
        foreach ($this->itemsWithDiscount as $item) {
            if ($item->id == $id) {
                if ($getValue) {
                    return $item->discount_applied;
                }

                return $item;
            }
        }

        return null;
    }

    /**
     * Model save method override (for session putting)
     *
     * @return void
     */
    public function save() : void
    {
        session([self::CART_KEY_ => serialize($this)]);
    }

    /**
     * Drop self in session
     *
     * @return void
     */
    public function destroy() : void
    {
        session([self::CART_KEY_ => false]);
    }

    /**
     * Catches the model
     *
     * @return mixed
     */
    public static function catch() // : mixed
    {
        $cart = unserialize(session(Cart::CART_KEY_));

        return $cart;
    }
}
