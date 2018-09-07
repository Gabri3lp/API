<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('12345678'),
        'remember_token' => str_random(10),
        'firstName' => $faker->firstName(null),
        'lastName' => $faker->lastName,
        'city' => $faker->city,
        'status' => $faker->randomElement($array = array ('Activo','Reposo')),
        'id' => $faker->unique()->randomNumber($nbDigits = 8),
        'address' => $faker->address,
        'phone' => $faker->phoneNumber,
        'cellphone' => $faker->tollFreePhoneNumber,
        'birthDate' => $faker->date($format = 'd-m-Y', $max = 'now'),
        'role_id' => $faker->randomElement($array = array ('1','2','3')),
    ];
});
