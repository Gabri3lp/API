<?php

use Faker\Generator as Faker;

$factory->define(Model::class, function (Faker $faker) {
    
    /*$startingDate = $faker->dateTimeThisYear('+1 month');
    $endingDate   = strtotime('+1 Week', $startingDate->getTimestamp());
    return [
            'user_id' =>  $faker->randomElement(User::all()->pluck('id')),
            'initialDate' => $startingDate, 
            'finalDate' => $endingDate, 
            'description' => $faker->text , 
            'status' => $faker->randomElement(), 
            'total'
            'user_id' => $faker->unique()->safeEmail,
            'password' => bcrypt('12345678'),
            'remember_token' => str_random(10),
            'firstName' => $faker->firstName(null),
            'lastName' => $faker->lastName,
            'city' => $faker->city,
            'status' => $faker->randomElement($array = array ('Activo','Inactivo')),
            'id' => $faker->unique()->randomNumber($nbDigits = 8),
            'address' => $faker->address,
            'phone' => $faker->phoneNumber,
            'cellphone' => $faker->tollFreePhoneNumber,
            'birthDate' => $faker->date($format = 'd-m-Y', $max = 'now'),
            'role_id' => $faker->randomElement($array = array ('2','3')),
    ];*/
});
