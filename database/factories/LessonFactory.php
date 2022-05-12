<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;

use Faker\Generator as Faker;

$factory->define(\App\Lesson::class, function (Faker $faker) {
    return [
        'title' => "Lesson {$faker->word}",
        'course_id' => \App\Course::all()->random()->id
    ];
});
