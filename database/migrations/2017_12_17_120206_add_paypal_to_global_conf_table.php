<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaypalToGlobalConfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('global_conf', function (Blueprint $table) {
            $table->integer('paypal')
            ->comment('Comission Paypal')->after('pound_exchange');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('global_conf', function (Blueprint $table) {
            $table->removeColumn('paypal');
        });
    }
}
