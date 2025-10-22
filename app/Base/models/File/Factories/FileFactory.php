<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Base\Models\File;
use Faker\Generator as Faker;

$factory->define(File::class, function (Faker $faker) {
    
    return [
        'src' => '/files/'.$faker->unique()->numberBetween(1, 1000).'.jpg',
        'external' => 'false',
        'position' => $faker->numberBetween(0, 255),
    ];
});
