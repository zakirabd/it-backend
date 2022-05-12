<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\TeacherEnroll::class, function (Faker $faker) {
    return [
        'student_id' => '',
        'teacher_id' => '',
        'lesson_mode' => '',
        'lesson_houre' => $faker->randomElement([10, 25, 36, 14]),
        'study_mode' => 'online',
        'student_group_id' => \App\StudentGroup::all()->random()->id,
        'fee' => 500
    ];
});
