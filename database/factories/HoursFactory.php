<?php

use Faker\Generator as Faker;

$factory->define(App\Hour::class, function (Faker $faker) {
    return [
        'user_id' => $faker->randomElement(App\User::pluck('id', 'id')->toArray()),
        'initialDate' => $faker->randomElement($array = array ('2018-02-17 05:00:00','2018-02-17 06:00:00', '2018-02-17 04:00:00')),
        'finalDate' => $faker->randomElement($array = array ('2018-02-17 07:00:00','2018-02-17 08:00:00', '2018-02-17 09:00:00')),
        'status' => $faker->randomElement($array = array ('En Desarrollo','Completado', 'Cancelada')),
        'description' => $faker->realText(180)
    ];
});
