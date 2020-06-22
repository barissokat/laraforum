<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Channel;
use App\Thread;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Thread::class, function (Faker $faker) {
    $title = $faker->sentence;

    return [
        'user_id' => factory(\App\User::class),
        'channel_id' => factory(\App\Channel::class),
        'title' => $title,
        'body' => $faker->paragraph,
        'visits' => 0,
        'slug' => Str::slug($title, '-'),
        'locked' => false,
    ];
});

$factory->state(Thread::class, 'from_existing_channels_and_users', function ($faker) {
    $title = $faker->sentence;

    return [
        'user_id' => function () {
            return User::all()->random()->id;
        },
        'channel_id' => function () {
            return Channel::all()->random()->id;
        },
        'title' => $title,
        'body'  => $faker->paragraph,
        'visits' => $faker->numberBetween(0, 35),
        'slug' => Str::slug($title, '-'),
        'locked' => $faker->boolean(15)
    ];
});
