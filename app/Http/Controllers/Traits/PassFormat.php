<?php
/**
 * Created by PhpStorm.
 * User: moussa
 * Date: 30/11/17
 * Time: 9:52
 */
namespace App\Http\Controllers\Traits;

trait PassFormat
{

    /**
     * @param array $passes
     * @return array
     */
    public function setFormat($passes, $product = false)
    {
        $data = [];

        foreach ($passes as $key => $pass) {
            $data[$key] = [
                'id' => (int)$pass->id,
                'productId' => (int)$pass->product_id,
                'productTitle' => $pass->product->name,
                'description' => $pass->product->description,
                'file' => '/images/' .$pass->product->file,//env('APP_URL') .
                'day' => $pass->day,
                'fecha' => $pass->datetime,
                'date' => $pass->date,
                'next' => $pass->next,
                'seatTypes' => [ 'data' => $this->setFormatPassPrice($pass->passes_seat_types)],
                "products" => '_Ocolutado por rendimiento'
            ];

            if ($product) {
                array_merge($data[$key], ['seatTypes' => ['data' => $pass->seatTypes]]);
                array_merge($data[$key], ['product' => ['data' => $pass->products]]);
            }
        }

        if ($product) {
            return $data[0] ?? [];
        }

        return $data;
    }

    /**
     * return ticketTypes with price
     *
     * @param [type] $passPrices
     * @return array
     */
    private function setFormatPassPrice($passes_seat_types)
    {
        $ticketTypeWithPrice = [];
        foreach ($passes_seat_types as $i => $passSeatType) {
            $ticketTypes = [];
            foreach ($passSeatType->passes_prices as $j => $ticketType) {
                $ticketTypes[] = [
                    "id" => $ticketType->id,
                    "ticketTypesId" => $ticketType->ticket_type_id,
                    "ticketType" => $ticketType->ticketType->title,
                    "price" => $ticketType->price
                ];
            }
            $ticketTypeWithPrice[] = [
                "id" => $passSeatType->id,
                "seatTypesId" => $passSeatType->seat_type_id,
                "seatType" => $passSeatType->seatType->title,
                "file" => $passSeatType->seatType->file,
                "_availability_comment" => 'Pendiente de traer la cantidad',
                "_availability" => [
                    "total_seats" => 100,
                    "used_seats" => null,
                    "available_seats" => $passSeatType->seats_available,
                ],
                "availability" => $passSeatType->seats_available,//TODO disponibilidad ***
                "ticketTypes" => ['data' => $ticketTypes]
            ];
        }

        return $ticketTypeWithPrice;
    }

    /**
     * Undocumented function
     *
     * @param [type] $seatTypes
     * @return array
     */
    private function getTicketTypesWithPrices($seatTypes)
    {
        $seatTypesWithPivot = [];
        foreach($seatTypes as $key => $seatType){
            $seatTypesWithPivot[] = $seatType->pivot->ticketTypes;
        }

        return $seatTypesWithPivot;
    }
}
