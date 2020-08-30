<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocodesModuleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** Create Shows table */
        Schema::create('promocodes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('code');

            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();

            $table->dateTime('for_from')->nullable();
            $table->dateTime('for_to')->nullable();

            $table->decimal('discount');

            $table->boolean('single_use');
            $table->boolean('canceled');

            $table->timestamps();
        });

        Schema::create('promocode_show', function (Blueprint $table) {
            $table->integer('promocode_id')->unsigned();
            $table->foreign('promocode_id')->references('id')->on('promocodes')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('show_id')->unsigned();
            $table->foreign('show_id')->references('id')->on('shows')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        /** Alter Reservations table with the associated promocode */
        Schema::table(config('crs.reservations-table'), function (Blueprint $table) {
            $table->integer('promocode_id')->unsigned()->after('id')->nullable();
            $table->foreign('promocode_id')->references('id')->on('promocodes')
                ->onDelete('restrict')->onUpdate('cascade');

            $table->integer('discount')->unsigned()->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** Delete Foreing Key, ticket adn seat types id columns from Reservations Tickets */
        Schema::table(config('crs.reservations-table'), function (Blueprint $table) {
            $table->dropForeign(config('crs.reservations-table') . '_promocode_id_foreign');
            $table->dropColumn('promocode_id');

            $table->dropColumn('discount');
        });

        /** Delete NM Promocode - Show table */
        Schema::dropIfExists('promocode_show');

        /** Delete Promocodes table */
        Schema::dropIfExists('promocodes');
    }
}
