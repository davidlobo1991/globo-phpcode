<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalConfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_conf', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('amber_trigger')
                    ->comment('Cantidad de asientos para que se muestre en naranja en el listado');
            $table->decimal('family_discount', 10, 2)
                    ->comment('Se añadirá como descuento por familia numerosa');
            $table->decimal('gold_discount', 10, 2)
                    ->comment('Se añadirá este descuento para clientes Gold');
            $table->decimal('booking_fee', 10, 2)
                    ->comment('Para todos los pases se aplicará este añadido al final de todo');
            $table->decimal('pound_exchange', 10, 8)
                    ->comment('Precio de una libra en euros');
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
        Schema::drop('global_conf');
    }
}
