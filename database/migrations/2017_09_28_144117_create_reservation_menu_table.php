<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
               /** Reservation Transports associated with Reservation table */
               Schema::create('reservation_menu', function (Blueprint $table) {
                $table->increments('id');
    
                $table->integer(config('crs.reservations-key'))->unsigned();
                $table->foreign(config('crs.reservations-key'))->references('id')->on(config('crs.reservations-table'))
                    ->onDelete('cascade')->onUpdate('cascade');
    
                $table->integer('menu_id')->unsigned();
                $table->foreign('menu_id')->references('id')->on('menus')
                    ->onDelete('restrict')->onUpdate('cascade');
    
                $table->integer('quantity');
                $table->decimal('price');
    
                $table->softDeletes();
                $table->timestamps();
            });
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
