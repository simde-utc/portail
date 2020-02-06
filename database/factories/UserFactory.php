<?php
/**
 * User factory
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2020, SiMDE-UTC
 * @license GNU GPL-3.0
 */

 use Faker\Generator as Faker;
 use App\Models\User;


 $factory->define(User::class, function (Faker $faker){
    return [
        'email' => $faker->safeEmail(),
        'firstname' => $faker->firstName(),
        'lastname' => $faker->lastName,
        'image' => config('seeder.user.generate_images') ? '/images/users/'.$faker->image('public/images/users', config('seeder.user.image_width'), config('seeder.user.image_height'), null, false) : null,
        'is_active' => $faker->boolean(90),
    ];
 });
