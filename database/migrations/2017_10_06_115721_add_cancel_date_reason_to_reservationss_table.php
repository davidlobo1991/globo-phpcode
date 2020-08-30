<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelDateReasonToReservationssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
           
            $table->timestamp('canceled_date')->nullable()->default(null)->after('canceled_by');
            $table->text('canceled_reason')->nullable()->after('canceled_date');
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
            $table->removeColumn('canceled_date');
            $table->removeColumn('canceled_reason');
        });
    }
}
