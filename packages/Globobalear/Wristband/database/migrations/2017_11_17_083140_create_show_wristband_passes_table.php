<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShowWristbandPassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('show_wristband_pass', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('wristband_pass_id')->unsigned();
            $table->foreign("wristband_pass_id")->references("id")->on("wristband_passes")->onDelete('cascade')->onUpdate('cascade');

            $table->integer('show_id')->unsigned();
            $table->foreign("show_id")->references("id")->on("shows")->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('show_wristband_pass');
    }
}
