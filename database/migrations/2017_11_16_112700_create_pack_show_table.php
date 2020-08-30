<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackShowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pack_show');
    }
}
