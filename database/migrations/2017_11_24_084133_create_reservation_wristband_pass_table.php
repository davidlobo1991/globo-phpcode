<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationWristbandPassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_wristband_pass', function (Blueprint $table) {
            $table->increments('id');

            
            $table->integer("reservation_id")->unsigned();
            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade')->onUpdate('cascade');

            $table->integer("wristband_pass_id")->unsigned();
            $table->foreign('wristband_pass_id')->references('id')->on('wristband_passes')->onDelete('cascade')->onUpdate('cascade');
            
            $table->integer('quantity')->default(0);

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
        Schema::dropIfExists('reservation_wristband_pass');
    }
}
