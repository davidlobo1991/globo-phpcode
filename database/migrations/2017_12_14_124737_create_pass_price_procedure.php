<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePassPriceProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        DELIMITER $$
            DROP PROCEDURE IF EXISTS `pass_price` $$
            CREATE PROCEDURE `pass_price`(IN
                pass INT(10), 
                ticket_type INT(10), 
                seat_type INT(10)
            )
            
            BEGIN
            
            SELECT id, pass_seat_type_id, ticket_type_id, price FROM passes_prices as pp
              WHERE pp.ticket_type_id = ticket_type 
              AND pp.pass_seat_type_id = (
                SELECT pst.id as pass_seat_type from pass_seat_type as pst 
                WHERE pst.seat_type_id = seat_type AND pst.pass_id = pass
              );
              
            END $$
            
           DELIMITER;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}