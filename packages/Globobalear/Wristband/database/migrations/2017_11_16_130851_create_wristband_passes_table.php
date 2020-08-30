<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWristbandPassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wristband_passes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title');
            $table->timestamp('date_start');
            $table->timestamp('date_end');
            $table->double('price', 6,2);
            $table->integer('quantity');

            $table->integer('wristband_id')->unsigned();
            $table->foreign("wristband_id")->references("id")->on("wristbands")->onUpdate('cascade')->onDelete('cascade');;

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
        Schema::dropIfExists('wristband_passes');
    }
}
