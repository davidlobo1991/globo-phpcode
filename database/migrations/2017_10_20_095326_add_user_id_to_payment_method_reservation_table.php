<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToPaymentMethodReservationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_method_reservations', function (Blueprint $table) {
            $table->integer("user_id")->unsigned()->nullable()->default(null)->after('total');
			$table->foreign("user_id")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_method_reservations', function (Blueprint $table) {
            $table->removeColumn('user_id');
        });
    }
}
