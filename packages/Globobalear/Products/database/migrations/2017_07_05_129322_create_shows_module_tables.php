<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowsModuleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** Create Shows table */
        Schema::create('shows', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            
            $table->string('acronym', 10);

            $table->text('description')->nullable();

            $table->string('image')->nullable();

            $table->integer('sort')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        /** Create passes table */
        Schema::create('passes', function (Blueprint $table) {
            $table->increments('id');

            $table->dateTime('datetime');

            $table->integer('show_id')->unsigned();
            $table->foreign('show_id')->references('id')->on('shows')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->string('comment')->nullable();
            $table->boolean('on_sale')->default(1);

            $table->timestamp('canceled_at');
            $table->softDeletes();
            $table->timestamps();
        });

        /** Create Seat Types table */
        Schema::create('seat_types', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('acronym', 10);

            $table->integer('default_quantity')->default(0)->unsigned();

            $table->boolean('is_enable')->default(1);
            $table->integer('sort')->unsigned();

            $table->softDeletes();
            $table->timestamps();
        });

        /** Create Ticket Types table */
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title', 150);
            $table->string('acronym', 10);

            $table->integer('take_place')->default(1)->unsigned();
            $table->integer('sort')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        /** Create the NM table between Seat Types and Shows */
        Schema::create('seat_type_show', function (Blueprint $table) {
            $table->integer('seat_type_id')->unsigned();
            $table->foreign('seat_type_id')->references('id')->on('seat_types')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('show_id')->unsigned();
            $table->foreign('show_id')->references('id')->on('shows')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('default_quantity')->default(0);
        });

        /** Create the NM table between Ticket Types and Shows */
        Schema::create('show_ticket_type', function (Blueprint $table) {
            $table->integer('show_id')->unsigned();
            $table->foreign('show_id')->references('id')->on('shows')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('ticket_type_id')->unsigned();
            $table->foreign('ticket_type_id')->references('id')->on('ticket_types')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        /** Create the NM table between Passes and Seat Types */
        Schema::create('pass_seat_type', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('pass_id')->unsigned();
            $table->foreign('pass_id')->references('id')->on('passes')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('seat_type_id')->unsigned();
            $table->foreign('seat_type_id')->references('id')->on('seat_types')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('seats_available')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        /** Create the NM table between Passes and Seat Types */
        Schema::create('passes_prices', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('pass_seat_type_id')->unsigned();
            $table->foreign('pass_seat_type_id')->references('id')->on('pass_seat_type')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('ticket_type_id')->unsigned();
            $table->foreign('ticket_type_id')->references('id')->on('ticket_types')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('price')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        /** Alter Reservations table with the associated pass */
        Schema::table(config('crs.reservations-table'), function (Blueprint $table) {
            $table->integer('pass_id')->unsigned()->after('id');
            $table->foreign('pass_id')->references('id')->on('passes')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        /** Alter Reservation Tickets table with the associated Ticket and Seat Types */
        Schema::table(config('crs.reservations-tickets-table'), function (Blueprint $table) {
            $table->integer('seat_type_id')->unsigned()->after('id');
            $table->foreign('seat_type_id')->references('id')->on('seat_types')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('ticket_type_id')->unsigned()->after('id');
            $table->foreign('ticket_type_id')->references('id')->on('ticket_types')
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
        /** Delete Foreing Key, ticket adn seat types id columns from Reservations Tickets */
        Schema::table(config('crs.reservations-tickets-table'), function (Blueprint $table) {
            $table->dropForeign(config('crs.reservations-tickets-table') . '_seat_type_id_foreign');
            $table->dropColumn('seat_type_id');

            $table->dropForeign(config('crs.reservations-tickets-table') . '_ticket_type_id_foreign');
            $table->dropColumn('ticket_type_id');
        });

        /** Delete Foreing Key and pass id column de Reservations */
        Schema::table(config('crs.reservations-table'), function (Blueprint $table) {
            $table->dropForeign(config('crs.reservations-table') . '_pass_id_foreign');
            $table->dropColumn('pass_id');
        });

        /** Delete NM Show - Ticket Types table */
        Schema::table('passes_prices', function (Blueprint $table) {
            $table->dropForeign('passes_prices_pass_seat_type_id_foreign');
            $table->dropForeign('passes_prices_ticket_type_id_foreign');
        });
        Schema::dropIfExists('passes_prices');

        /** Delete NM Show - Ticket Types table */
        Schema::table('pass_seat_type', function (Blueprint $table) {
            $table->dropForeign('pass_seat_type_pass_id_foreign');
            $table->dropForeign('pass_seat_type_seat_type_id_foreign');
        });
        Schema::dropIfExists('pass_seat_type');

        /** Delete NM Show - Ticket Types table */
        Schema::table('show_ticket_type', function (Blueprint $table) {
            $table->dropForeign('show_ticket_type_show_id_foreign');
            $table->dropForeign('show_ticket_type_ticket_type_id_foreign');
        });
        Schema::dropIfExists('show_ticket_type');

        /** Delete NM Show - Seat Types table */
        Schema::table('seat_type_show', function (Blueprint $table) {
            $table->dropForeign('seat_type_show_seat_type_id_foreign');
            $table->dropForeign('seat_type_show_show_id_foreign');
        });
        Schema::dropIfExists('seat_type_show');

        /** Delete Ticket Types table */
        Schema::dropIfExists('ticket_types');

        /** Delete Seat Types table */
        Schema::dropIfExists('seat_types');

        /** Delete Passes table */
        Schema::dropIfExists('passes');

        /** Delete Passes table */
        Schema::dropIfExists('shows');
    }
}
