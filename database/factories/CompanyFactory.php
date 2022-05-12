<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Company::class, function (Faker $faker) {

    $company_head = factory(\App\User::class)->create([
        'role' => 'company_head'
    ]);

    return [
        'name' => $faker->company,
        'company_avatar' => null,
        'description' => $faker->paragraph,
        'address' => $faker->address,
        'country' => $faker->century,
        'city' => $faker->city,
        'user_id' => $company_head->id
    ];
});
