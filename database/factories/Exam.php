<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Exam;
use App\Course;
use App\Lesson;

use Faker\Generator as Faker;

$factory->define(Exam::class, function (Faker $faker) {
    return [
        'title'=>$faker->jobTitle,
        'duration_minutes'=>$faker->numberBetween(100,200),
        'retake_minutes'=>$faker->numberBetween(100,200),
        'retake_time'=>$faker->numberBetween(100,200),
        'points'=>$faker->numberBetween(100,200),
        'description'=>$faker->sentence,
        'course_id' => Course::all()->random(),
        'lesson_id' => Lesson::all()->random(),


    ];
});
