<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMenusModuleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cartes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('is_enable')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('dishes_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('dishes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description_allergens')->nullable();
            $table->boolean('vegetarian')->default(0);
            $table->integer('dishes_type_id')->unsigned();
            $table->foreign('dishes_type_id')->references('id')->on('dishes_types')
                ->onDelete('no action')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        /** Create the NM table between Cartes and Menus **/
        Schema::create('carte_menu', function (Blueprint $table) {
            $table->integer('carte_id')->unsigned();
            $table->foreign('carte_id')->references('id')->on('cartes')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('menu_id')->unsigned();
            $table->foreign('menu_id')->references('id')->on('menus')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();    
        });

        /** Create the NM table between Menus and Dishes **/
        Schema::create('dish_menu', function (Blueprint $table) {
            $table->integer('menu_id')->unsigned();
            $table->foreign('menu_id')->references('id')->on('menus')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('dish_id')->unsigned();
            $table->foreign('dish_id')->references('id')->on('dishes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();    
        });

        /** Reservation Transports associated with Reservation table */
        Schema::create('reservation_menu', function (Blueprint $table) {
            $table->increments('id');

            $table->integer(config('crs.reservations-key'))->unsigned();
            $table->foreign(config('crs.reservations-key'))->references('id')->on(config('crs.reservations-table'))
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('menu_id')->unsigned();
            $table->foreign('menu_id')->references('id')->on('menus')
                ->onDelete('restrict')->onUpdate('cascade');

            $table->integer('quantity');
            $table->decimal('price');

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
        Schema::dropIfExists('dish_menu');
        Schema::dropIfExists('carte_menu');

        Schema::dropIfExists('dishes');
        Schema::dropIfExists('dishes_types');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('cartes');
    }
}
