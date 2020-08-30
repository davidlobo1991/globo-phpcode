<?php

use App\Language;
use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::create([
            'name' => 'English',
            'iso' => 'EN',
            'is_enable' => true,
        ]);

        Language::create([
            'name' => 'Español',
            'iso' => 'ES',
            'is_enable' => true,
        ]);
    }
}
