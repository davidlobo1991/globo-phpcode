<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResellersModuleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        Schema::create('resellers_types', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('name');
            $table->softDeletes();
			$table->timestamps();
        });
        
        Schema::create('agent_types', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('name');
            $table->softDeletes();
			$table->timestamps();
		});
       
        Schema::create('resellers', function (Blueprint $table) {
            $table->increments('id');
            
            $table->decimal('discount', 10, 2);
            
            $table->string('company');
            $table->string('name')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);
			$table->string('fax')->nullable()->default(null);
			$table->string('address')->nullable()->default(null);
			$table->string('city')->nullable()->default(null);
            $table->string('postal_code')->nullable()->default(null);
            $table->text('internal_comments')->nullable();

            $table->integer('passes_seller_id')->unsigned()->nullable();
            $table->foreign("passes_seller_id")->references("id")->on("passes_sellers")->onDelete('set null');

			$table->integer('resellers_type_id')->unsigned()->nullable();
            $table->foreign("resellers_type_id")->references("id")->on("resellers_types")->onDelete('set null');
            
            $table->integer('agent_type_id')->unsigned()->nullable();
			$table->foreign("agent_type_id")->references("id")->on("agent_types")->onDelete('set null');

			$table->integer('area_id')->unsigned()->nullable();
			$table->foreign("area_id")->references("id")->on("areas")->onDelete('set null');
			
			$table->integer('language_id')->unsigned()->nullable();
			$table->foreign("language_id")->references("id")->on("languages")->onDelete('set null');
            
            $table->integer('country_id')->unsigned()->nullable();
			$table->foreign("country_id")->references("id")->on("countries")->onDelete('set null');

			$table->integer('user_id')->unsigned()->nullable();
            $table->foreign("user_id")->references("id")->on("users")->onDelete('set null');

            $table->boolean('is_enable')->default(1);
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
        
        Schema::dropIfExists('resellers');
        Schema::dropIfExists('resellers_types');
        Schema::dropIfExists('agent_types');
        
    }
}
