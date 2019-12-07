<?php

use App\Models\Partner;
use Faker\Generator as Faker;

$factory->define(Partner::class, function (Faker $faker) {
    return [
        'name' => $faker->company(),
        'description' => $faker->text(config('seeder.partner.description_length')),
        'image' => '/images/partners/'.$faker->image('public/images/partners', config('seeder.partner.image_width'), config('seeder.partner.image_height'), null, false),
        'website' => $faker->url(),
        'address' => $faker->streetAddress(),
        'postal_code' => $faker->postcode(),
        'city' => $faker->city(),
    ];
});
