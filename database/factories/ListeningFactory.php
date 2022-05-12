<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Listening::class, function (Faker $faker) {
//    $course = \App\Course::all()->random();
    return [
        'title' => $faker->text(100),
        'audio_file' => '',
//        'course_id' => $course->id,
//        'lesson_id' => $course->lessons->random()->id
    ];
});
