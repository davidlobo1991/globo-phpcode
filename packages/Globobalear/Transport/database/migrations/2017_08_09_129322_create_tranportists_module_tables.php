<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranportistsModuleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** Transporters Table */
        Schema::create('transporters', function (Blueprint $table) {
           $table->increments('id');

           $table->string('name');
           $table->softDeletes(); 
           $table->timestamps();
        });

        /** Areas Table */
        Schema::create('areas', function (Blueprint $table) {
           $table->increments('id');

           $table->string('name', 150);
           $table->string('acronym', 10);
           $table->softDeletes();
           $table->timestamps();
        });

        /** Routes Table */
        Schema::create('routes', function (Blueprint $table) {
           $table->increments('id');

           $table->integer('area_id')->unsigned()->nullable();
           $table->foreign('area_id')->references('id')->on('areas')
               ->onDelete('set null')->onUpdate('cascade');

           $table->string('name', 150);
           $table->softDeletes();
           $table->timestamps();
        });

        /** Cities Table */
        Schema::create('cities', function(Blueprint $table){
            $table->increments('id');

            $table->string('name', 150);
            $table->softDeletes();
            $table->timestamps();
        });

        /** Pickup Points Table */
        Schema::create('pickup_points', function (Blueprint $table) {
           $table->increments('id');

           $table->string('name', 150);

           $table->string('latitude');
           $table->string('longitude');

           $table->string('mapaddress');

           $table->integer('city_id')->unsigned()->nullable();
           $table->foreign('city_id')->references('id')->on('cities')
               ->onDelete('set null')->onCascade('update');
           $table->softDeletes();
           $table->timestamps();
        });

        /** NM Pickup Points & Routes Table */
        Schema::create('pickup_point_route', function (Blueprint $table) {
           $table->integer('pickup_point_id')->unsigned();
           $table->foreign('pickup_point_id')->references('id')->on('pickup_points')
               ->onDelete('cascade')->onUpdate('cascade');

           $table->integer('route_id')->unsigned();
           $table->foreign('route_id')->references('id')->on('routes')
               ->onDelete('cascade')->onUpdate('cascade');

           $table->string('hour');
           $table->decimal('price');
           $table->string('order');
           $table->softDeletes(); 
           $table->timestamps();
        });

        /** Buses Table */
        Schema::create('buses', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('capacity')->default(0);

            $table->integer('transporter_id')->unsigned()->nullable();
            $table->foreign('transporter_id')->references('id')->on('transporters')
                ->onDelete('set null')->onUpdate('cascade');

            $table->integer('route_id')->unsigned();
            $table->foreign('route_id')->references('id')->on('routes')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('pass_id')->unsigned();
            $table->foreign('pass_id')->references('id')->on('passes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        /** Reservation Transports associated with Reservation table */
        Schema::create('reservation_transports', function (Blueprint $table) {
            $table->increments('id');

            $table->integer(config('crs.reservations-key'))->unsigned();
            $table->foreign(config('crs.reservations-key'))->references('id')->on(config('crs.reservations-table'))
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('bus_id')->unsigned();
            $table->foreign('bus_id')->references('id')->on('buses')
                ->onDelete('restrict')->onUpdate('cascade');

            $table->integer('pickup_point_id')->unsigned();
            $table->foreign('pickup_point_id')->references('id')->on('pickup_points')
                ->onDelete('restrict')->onUpdate('cascade');

            $table->integer('quantity');
            $table->decimal('price');

            $table->string('pickup_point', 150);
            $table->string('hour', 100);
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
        /** Drop all the tables */
        Schema::dropIfExists('reservation_transports');
        Schema::dropIfExists('buses');
        Schema::dropIfExists('pickup_point_route');
        Schema::dropIfExists('pickup_points');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('transporters');
    }
}
