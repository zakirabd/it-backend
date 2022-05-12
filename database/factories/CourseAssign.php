<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\CourseAssign;
use App\Course;
use App\Company;
use Faker\Generator as Faker;

$factory->define(CourseAssign::class, function (Faker $faker) {
    return [
        'companie_id' => Company::all()->random(),
        'course_id' => Course::all()->random(),
    ];
});
