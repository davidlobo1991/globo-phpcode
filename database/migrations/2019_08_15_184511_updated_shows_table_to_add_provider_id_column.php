<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatedShowsTableToAddProviderIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() : void
    {
        Schema::table(
            'shows', function (Blueprint $table) {
                $table->integer('provider_id')->unsigned()->nullable();
                $table->foreign('provider_id')
                    ->references('id')
                    ->on('providers')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::table(
            'shows', function (Blueprint $table) {
                $table->dropForeign('shows_provider_id_foreign');
                $table->dropColumn('provider_id');
            }
        );
    }
}
