<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReservationsAddUnfinished extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->boolean('finished')->default(0)->after('reference_number');
            $table->integer('canceled_by')->unsigned()->nullable()->after('reference_number');
            $table->foreign('canceled_by', 'canceled_by_users')->references('id')->on('users')
                ->onDelete('set null')->onUpdate('cascade');
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
            $table->dropForeign('canceled_by_users');
            $table->dropColumn('canceled_by');
            $table->dropColumn('canceled_date');
            $table->dropColumn('finished');
        });
    }
}
