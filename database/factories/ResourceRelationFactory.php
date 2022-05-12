<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\ResourceRelations::class, function (Faker $faker) {
    $entity = factory(\App\Resource::class)->make();

    return [
        'resource_id' => $entity->id,
        'role' => $faker->randomElement(['chief_auditor', 'auditor', 'content_manager', 'office_manager'])
    ];
    
});
