<?php
/**
 * Assos factory
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2020, SiMDE-UTC
 * @license GNU GPL-3.0
 */

use App\Models\AssoType;
use App\Models\Asso;
use Webpatser\Uuid\Uuid;
use Faker\Generator as Faker;

$factory->define(Asso::class, function (Faker $faker) {

    $assoTypeIdentifiers = explode(',', config("seeder.asso.type.identifiers"));
    $assoParents = explode(',', config("seeder.asso.parents"));
    $shortname = $faker->company();
    return [
        'id' => Uuid::generate()->string,
        'type_id' => AssoType::where('type', $faker->randomElement($assoTypeIdentifiers))->first()->id,
        'parent_id' => Asso::where('login', $faker->randomElement($assoParents))->first()->id,
        'login' => $faker->regexify('\w{6,30}'),
        'shortname' => $shortname,
        'name' => ($faker->boolean(50) ? $shortname.' '.$faker->companySuffix() : null),
        'image' => config("seeder.asso.generate_images", false) ? '/images/assos/'.$faker->image('public/images/assos', config('seeder.asso.image_width'), config('seeder.asso.image_height'), null, false) : null ,
        'short_description' => $faker->catchPhrase(),
        'description' => $faker->realText(400, 2),
        'in_cemetery_at' => $faker->boolean(20) ? $faker->dateTimeBetween('-5 years', 'now') : null,
    ];
});
