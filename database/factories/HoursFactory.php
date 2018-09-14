<?php

use Faker\Generator as Faker;
use Carbon\Carbon;
$factory->define(App\Hour::class, function (Faker $faker) {
    //$initialDate = $faker->dateTime();
    //$total = $faker->numberBetween(1, 9);
    $year = rand(2009, 2016);
    $month = rand(1, 12);
    $day = rand(1, 28);
    $hour = rand(1, 24);
    $min = rand(0, 59);
    $initialDate = Carbon::create($year,$month ,$day , $hour, $min, 0);

    return [
        'user_id' => $faker->randomElement(App\User::pluck('id', 'id')->toArray()),
        'initialDate' => $initialDate->format('Y-m-d H:i:s'),
        'finalDate' => $initialDate->addHours(rand(1, 9)),
        'status' => $faker->randomElement($array = array ('En Desarrollo','Completado', 'Cancelada')),
        'description' => $faker->realText(180)
    ];
});
