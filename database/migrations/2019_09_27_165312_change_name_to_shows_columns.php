<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameToShowsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cartes', function (Blueprint $table) {
            $table->renameColumn('show_id', 'product_id');
        });

        Schema::table('pack_products', function (Blueprint $table) {
            $table->renameColumn('show_id', 'product_id');
        });

        Schema::table('reservation_packs', function (Blueprint $table) {
            $table->renameColumn('show_id', 'product_id');
        });

        Schema::table('product_seat_type', function (Blueprint $table) {
           $table->renameColumn('show_id', 'product_id');
        });

        Schema::table('product_ticket_type', function (Blueprint $table) {
            $table->renameColumn('show_id', 'product_id');
        });

        Schema::table('passes', function (Blueprint $table) {
            $table->renameColumn('show_id', 'product_id');
        });

        Schema::table('product_wristband_pass', function (Blueprint $table) {
            $table->renameColumn('show_id', 'product_id');
        });

        Schema::table('product_promocode', function (Blueprint $table) {
            $table->renameColumn('show_id', 'product_id');
        });


    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cartes', function (Blueprint $table) {
            $table->renameColumn('product_id', 'show_id');
        });

        Schema::table('pack_products', function (Blueprint $table) {
            $table->renameColumn('product_id', 'show_id');
        });

        Schema::table('reservation_packs', function (Blueprint $table) {
            $table->renameColumn('product_id', 'show_id');
        });

        Schema::table('product_seat_type', function (Blueprint $table) {
            $table->renameColumn('product_id', 'show_id');
        });

        Schema::table('product_ticket_type', function (Blueprint $table) {
            $table->renameColumn('product_id', 'show_id');
        });

        Schema::table('passes', function (Blueprint $table) {
            $table->renameColumn('product_id', 'show_id');
        });

        Schema::table('product_wristband_pass', function (Blueprint $table) {
            $table->renameColumn('product_id', 'show_id');
        });

        Schema::table('product_promocode', function (Blueprint $table) {
            $table->renameColumn('product_id', 'show_id');
        });

    }
}
