<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatedPassesTableToAddWebAvailableField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() : void
    {
        Schema::table(
            'passes', function (Blueprint $table) {
                $table->boolean('web_available')->default(true);
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
            'passes', function (Blueprint $table) {
                $table->dropColumn('web_available');
            }
        );
    }
}
