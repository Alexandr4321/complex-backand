<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Auth\Models\Grant;
use Faker\Generator as Faker;

$factory->define(Grant::class, function (Faker $faker) {
    return [
        'modelType' => null,
    ];
});
