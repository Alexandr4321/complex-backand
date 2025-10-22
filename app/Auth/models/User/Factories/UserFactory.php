<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Auth\Models\User;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->randomElement([ '707', '777', '747', '701', '705', ]).$faker->unique()->numberBetween(1000000, 9999999),
        'password' => '123123',
        'firstName' => $faker->name(),
        'lastName' => $faker->name(),

    ];
});

$factory->afterCreating(User::class, function($model) {
    $model->password = bcrypt('123123');
    $model->save();
});
