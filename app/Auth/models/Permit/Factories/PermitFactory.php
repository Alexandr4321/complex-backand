<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Auth\Models\Permit;
use Faker\Generator as Faker;

$factory->define(Permit::class, function (Faker $faker) {
    return [
        'modelType' => null,
        'description' => null,
    ];
});
