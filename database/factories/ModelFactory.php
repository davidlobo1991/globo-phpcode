<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(Globobalear\Products\Models\Product::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'acronym' => $faker->text(5),
        'description' => [
            'en' => $faker->text(100),
            'es' => $faker->text(100),
        ],
    ];
});

$factory->define(Globobalear\Products\Models\Pass::class, function (Faker\Generator $faker) {
    return [
        'datetime' => $faker->dateTime,
        'comment' => $faker->sentence(),
        'on_sale' => $faker->boolean,
    ];
});

$factory->define(Globobalear\Products\Models\SeatType::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->name,
        'description' => [
            'en' => $faker->text(100),
            'es' => $faker->text(100),
        ],
        'acronym' => $faker->text(5),
        'default_quantity' => 100,
        'is_enable' => true
    ];
});

$factory->define(Globobalear\Products\Models\TicketType::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->name,
        'acronym' => $faker->text(5),
        'take_place' => $faker->boolean(66)
    ];
});
