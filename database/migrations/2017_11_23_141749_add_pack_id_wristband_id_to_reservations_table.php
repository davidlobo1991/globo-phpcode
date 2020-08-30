<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPackIdWristbandIdToReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->integer("pack_id")->unsigned()->nullable()->after('pass_id');
            $table->foreign('pack_id')->references('id')->on('packs')
            ->onDelete('cascade')->onUpdate('cascade');
            $table->integer("wristband_pass_id")->unsigned()->nullable()->after('pack_id');
            $table->foreign('wristband_pass_id')->references('id')->on('wristband_passes')
            ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->removeColumn('pack_id');
            $table->removeColumn('wristband_pass_id');
        });
    }
}
