<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsModuleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** Create Payments Methods table */
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('image')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('payment_method_reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_method_id')->unsigned();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer(config('crs.reservations-foreign'))->unsigned();
            $table->foreign(config('crs.reservations-foreign'))->references('id')->on(config('crs.reservations-table'))
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer("user_id")->unsigned()->nullable()->default(null)->after('id');
            $table->foreign("user_id")->references("id")->on("users");    

            $table->decimal('total');
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
        /** Delete NM Payment Method Reservation table */
        Schema::dropIfExists('payment_method_reservations');

        /** Delete Payments Methods table */
        Schema::dropIfExists('payment_methods');

    }
}
