<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameToShowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('shows', 'products');
        Schema::rename('pack_shows', 'pack_products');
        Schema::rename('seat_type_show', 'product_seat_type');
        Schema::rename('show_ticket_type', 'product_ticket_type');
        Schema::rename('show_wristband_pass', 'product_wristband_pass');
        Schema::rename('promocode_show', 'product_promocode');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('products', 'shows');
        Schema::rename('pack_products', 'pack_shows');
        Schema::rename('product_seat_type', 'seat_type_show');
        Schema::rename('product_ticket_type', 'show_ticket_type');
        Schema::rename('product_wristband_pass', 'show_wristband_pass');
        Schema::rename('product_promocode', 'promocode_show');
    }
}
