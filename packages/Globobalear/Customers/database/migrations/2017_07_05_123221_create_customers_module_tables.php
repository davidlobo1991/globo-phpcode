<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomersModuleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('customers_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',2);
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('customers_how_you_meet_us', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            /** PRIMARY KEY **/
            $table->increments('id');

            $table->string('name');
            $table->string('identification_number')->nullable();
            $table->datetime('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('town')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('email')->nullable();

            $table->text('internal_comments')->nullable();

            /** FOREIGN KEYS **/
            $table->integer('gender_id')->unsigned()->nullable();
            $table->foreign('gender_id')->references('id')->on('genders')
                ->onDelete('set null')->onUpdate('cascade');

            $table->integer('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')
                ->onDelete('set null')->onUpdate('cascade');

            $table->integer('customer_language_id')->unsigned()->nullable();
            $table->foreign('customer_language_id')->references('id')->on('customers_languages')
                ->onDelete('set null')->onUpdate('cascade');

            $table->integer('customer_how_you_meet_us_id')->unsigned()->nullable();
            $table->foreign('customer_how_you_meet_us_id')->references('id')->on('customers_how_you_meet_us')
                ->onDelete('set null')->onUpdate('cascade');


            $table->boolean('is_enabled')->default(1);
            $table->boolean('newsletter')->default(0);
            $table->boolean('resident')->default(0);
            $table->softDeletes();
           
            $table->timestamps();
        });

        Schema::table(config('crs.reservations-table'), function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('phone')->nullable()->after('id');
            $table->string('email')->after('id');
            $table->string('identification_number')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('crs.reservations-table'), function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('phone');
            $table->dropColumn('email');
            $table->dropColumn('identification_number');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign('customers_gender_id_foreign');
            $table->dropColumn('gender_id');

            $table->dropForeign('customers_customer_language_id_foreign');
            $table->dropColumn('customer_language_id');

            $table->dropForeign('customers_country_id_foreign');
            $table->dropColumn('country_id');

            $table->dropForeign('customers_customer_how_you_meet_us_id_foreign');
            $table->dropColumn('customer_how_you_meet_us_id');
        });

        Schema::dropIfExists('customers');
        Schema::dropIfExists('genders');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('customers_how_you_meet_us');
        Schema::dropIfExists('customers_languages');
    }
}
