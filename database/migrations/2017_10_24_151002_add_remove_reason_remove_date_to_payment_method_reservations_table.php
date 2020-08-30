<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemoveReasonRemoveDateToPaymentMethodReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_method_reservations', function (Blueprint $table) {
            $table->integer("removed_by")->unsigned()->nullable()->default(null)->after('total');
            $table->foreign("removed_by")->references("id")->on("users"); 

            $table->timestamp('removed_date')->nullable()->default(null)->after('removed_by');
            $table->text('removed_reason')->nullable()->after('removed_date');
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
            $table->removeColumn('removed_date');
            $table->removeColumn('removed_reason');
        });
    }
}
