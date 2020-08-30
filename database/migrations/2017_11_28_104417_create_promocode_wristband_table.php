<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromocodeWristbandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocode_wristband', function (Blueprint $table) {

            Schema::dropIfExists('promocode_wristband_pass');

            $table->increments('id');

            $table->integer('promocode_id')->unsigned();
            $table->foreign('promocode_id')->references('id')->on('promocodes')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('wristband_id')->unsigned();
            $table->foreign('wristband_id')->references('id')->on('wristbands')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('promocode_wristband');
    }
}
