<?php

namespace App\Models\Web;

class Product
{
//    protected $appends = [
//        'realPassId', 'seatTypeId', 'passId', 'crsCat', 'seatTypeName', 'passName', 'price', 'quantity', 'image'
//    ];

    private $realPassId, $seatTypeId, $passId, $crsCat, $price, $quantity, $productType;


    /**
     * Producto constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->realPassId = $attributes['realPassId'] ?? null;
        $this->seatTypeId = $attributes['seatTypeId'] ?? null;
        $this->passId = $attributes['passId'] ?? null;
        $this->crsCat = $attributes['crsCat'] ?? null;
        $this->price = $attributes['price'] ?? null;
        $this->quantity = $attributes['quantity'] ?? null;
        $this->productType = $attributes['productType'] ?? null;
    }

    /**
     * @param mixed $realPassId
     */
    public function setRealPassId($realPassId)
    {
        $this->realPassId = $realPassId;
    }

    /**
     * @param mixed $seatTypeId
     */
    public function setSeatTypeId($seatTypeId)
    {
        $this->seatTypeId = $seatTypeId;
    }

    /**
     * @param mixed $passId
     */
    public function setPassId($passId)
    {
        $this->passId = $passId;
    }

    /**
     * @param mixed $crsCat
     */
    public function setCrsCat($crsCat)
    {
        $this->crsCat = $crsCat;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed|null
     */
    public function getRealPassId(): mixed
    {
        return $this->realPassId;
    }

    /**
     * @return mixed|null
     */
    public function getSeatTypeId(): string
    {
        return $this->seatTypeId;
    }

    /**
     * @return mixed|null
     */
    public function getPassId(): int
    {
        return $this->passId;
    }

    /**
     * @return mixed|null
     */
    public function getCrsCat(): mixed
    {
        return $this->crsCat;
    }

    /**
     * @return mixed|null
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return mixed|null
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

}
