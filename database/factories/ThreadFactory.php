<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Thread;
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
