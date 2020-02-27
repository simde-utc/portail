<?php
/**
 * Partner factory
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2020, SiMDE-UTC
 * @license GNU GPL-3.0
 */

use App\Models\Partner;
use Faker\Generator as Faker;

$factory->define(Partner::class, function (Faker $faker) {
    return [
        'name' => $faker->company(),
        'description' => $faker->text(config('seeder.partner.description_length')),
        'image' => config('seeder.partner.generate_images') ? '/images/partners/'.$faker->image('public/images/partners', config('seeder.partner.image_width'), config('seeder.partner.image_height'), null, false) : null,
        'website' => $faker->url(),
        'address' => $faker->streetAddress(),
        'postal_code' => $faker->postcode(),
        'city' => $faker->city(),
    ];
});
