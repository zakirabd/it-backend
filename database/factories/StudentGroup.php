<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\StudentGroup;
use Faker\Generator as Faker;

$factory->define(StudentGroup::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'code' => $faker->randomElement(['#FFA07A', '#800000', '#808000', '#00FF00', '#008080', '#FF00FF']),
    ];
});
