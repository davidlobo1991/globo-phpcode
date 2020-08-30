<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crea el usuario por defecto del CRS
        $this->call(AdminUserSeeder::class);

        // Crea los idiomas por defecto del CRS
        $this->call(LanguagesSeeder::class);

        // Genera 4 tipos de asientos en la BD
        // factory(Globobalear\Products\Models\SeatType::class, 4)->create();

        // Genera 3 tipos de tickets en la BD
        // factory(Globobalear\Products\Models\TicketType::class, 3)->create();

        // Genera 3 shows de prueba en la BD
        // factory(Globobalear\Products\Models\Product::class, 3)->create()->each(function($show) {
        //     $show->passes()->saveMany(factory(Globobalear\Products\Models\Pass::class, 10)->make());
        // });
    }
}
