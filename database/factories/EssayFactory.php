<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Course;
use App\Lesson;
use Faker\Generator as Faker;

$factory->define(\App\Essay::class, function (Faker $faker) {
    //$course = Course::all()->random();
    //$lesson = Lesson::where('course_id', $course->id)->select('id', 'course_id')->get();
//    dd($lesson->random());
    return [
        'title' => $faker->sentence,
        'essay_type' => 'unit',
        'question' => $faker->sentence,
        'course_id' => Course::all()->random(),
//        'lesson_id' => $course->lessons->random()->id
        'lesson_id' => Lesson::all()->random()->id
    ];
});
