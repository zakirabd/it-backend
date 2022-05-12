<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\CourseUser::class, function (Faker $faker) {
    return [
        'course_id' => '',
        'user_id' => ''
    ];
});
