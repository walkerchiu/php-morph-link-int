<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\MorphLink\Models\Entities\Link;
use WalkerChiu\MorphLink\Models\Entities\LinkLang;

$factory->define(Link::class, function (Faker $faker) {
    return [
        'type'   => $faker->randomElement(['link', 'blog', 'facebook', 'instagram', 'twitter']),
        'serial' => $faker->isbn10,
        'url'    => $faker->url
    ];
});

$factory->define(LinkLang::class, function (Faker $faker) {
    return [
        'code'  => $faker->locale,
        'key'   => $faker->randomElement(['name', 'description']),
        'value' => $faker->sentence
    ];
});
