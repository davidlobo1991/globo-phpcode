<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationPackWristbandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_pack_wristbands', function (Blueprint $table) {
            $table->increments('id');

            $table->integer(config('crs.reservations-key'))->unsigned();
            $table->foreign(config('crs.reservations-key'))->references('id')->on(config('crs.reservations-table'))
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('pack_id')->unsigned();
            $table->foreign('pack_id')->references('id')->on('packs')
                ->onDelete('restrict')->onUpdate('cascade');

            $table->integer('wristband_passes_id')->unsigned();
            $table->foreign('wristband_passes_id')->references('id')->on('wristband_passes')
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
        Schema::dropIfExists('reservation_pack_wristbands');
    }
}
