<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Resource::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'status' => $faker->randomNumber([0, 1]),
        'attachment' => $faker->file('', '', ''),
    ];
});
