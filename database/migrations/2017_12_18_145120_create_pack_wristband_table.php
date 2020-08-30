<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackWristbandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pack_wristbands', function (Blueprint $table) {
            
            $table->integer('pack_id')->unsigned();
            $table->foreign('pack_id')->references('id')->on('packs')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('wristband_passes_id')->unsigned();
            $table->foreign('wristband_passes_id')->references('id')->on('wristband_passes')
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
        Schema::dropIfExists('pack_wristbands');
    }
}
