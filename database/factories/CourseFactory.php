<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Course::class, function (Faker $faker) {
    return [
        'title' => $faker->jobTitle,
        'level' => $faker->randomElement(['A', 'B', 'C', 'D', 'F', 'G', 'H']),
        'grade' => $faker->randomNumber(),
        'image_url' => null,
    ];
});
