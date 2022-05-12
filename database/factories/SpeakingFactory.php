<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Course;
use App\Lesson;
use Faker\Generator as Faker;

$factory->define(\App\Speaking::class, function (Faker $faker) {
    //$course = Course::all()->random();

    return [
        'title' => $faker->word,
        'speaking_type' => 'Type',
        'question' => $faker->sentence,
        'course_id' => Course::all()->random(),
//        'lesson_id' => $course->lessons->random()->id
        'lesson_id'=>function(){
            return Lesson::all()->random();
        },
    ];
});
