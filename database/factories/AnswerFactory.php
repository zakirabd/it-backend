<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Answer::class, function (Faker $faker) {
    return [
        'question_id' => 1,
        'title' => $faker->text(20),
        'is_correct' => ''
    ];
});
