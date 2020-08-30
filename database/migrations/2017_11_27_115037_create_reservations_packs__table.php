<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_packs', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer(config('crs.reservations-key'))->unsigned();
            $table->foreign(config('crs.reservations-key'))->references('id')->on(config('crs.reservations-table'))
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('pack_id')->unsigned();
            $table->foreign('pack_id')->references('id')->on('packs')
                ->onDelete('restrict')->onUpdate('cascade');

            $table->integer('show_id')->unsigned();
            $table->foreign('show_id')->references('id')->on('shows')
                ->onDelete('restrict')->onUpdate('cascade');

            $table->integer('seat_type_id')->unsigned();
            $table->foreign('seat_type_id')->references('id')->on('seat_types')
                ->onDelete('restrict')->onUpdate('cascade');

            $table->integer('ticket_type_id')->unsigned();
            $table->foreign('ticket_type_id')->references('id')->on('ticket_types')
                ->onDelete('restrict')->onUpdate('cascade');

            $table->integer('pass_id')->unsigned();
            $table->foreign('pass_id')->references('id')->on('passes')
            ->onDelete('restrict')->onUpdate('cascade');


            $table->integer('quantity');
            $table->decimal('unit_price');

            
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
        Schema::dropIfExists('reservation_packs');
    }
}
