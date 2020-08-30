<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePacksModuleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      
        Schema::create('packs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('acronym', '5');
            $table->dateTime('date_start')->nullable();
            $table->dateTime('date_end')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('pack_shows', function (Blueprint $table) {
            
            $table->integer('pack_id')->unsigned();
            $table->foreign('pack_id')->references('id')->on('packs')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('show_id')->unsigned();
            $table->foreign('show_id')->references('id')->on('shows')
                ->onDelete('cascade')->onUpdate('cascade');  
                  

            $table->integer('seat_type_id')->unsigned();
            $table->foreign('seat_type_id')->references('id')->on('seat_types')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('ticket_type_id')->unsigned();
            $table->foreign('ticket_type_id')->references('id')->on('ticket_types')
                ->onDelete('cascade')->onUpdate('cascade');    

            $table->integer('price')->default(0);    

            
        });
        
        /** Reservation Transports associated with Reservation table */
        Schema::create('reservation_packs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer(config('crs.reservations-key'))->unsigned();
            $table->foreign(config('crs.reservations-key'))->references('id')->on(config('crs.reservations-table'))
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('pack_id')->unsigned();
            $table->foreign('pack_id')->references('id')->on('packs')
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
        /** Drop all the tables */
        Schema::dropIfExists('reservation_packs');
        Schema::dropIfExists('packs');
        Schema::dropIfExists('pack_shows');
       
    }
}
