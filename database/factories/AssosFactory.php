<?php

use App\Models\AssoType;
use App\Models\Asso;
use Webpatser\Uuid\Uuid;
use Faker\Generator as Faker;

$factory->define(Asso::class, function (Faker $faker) {

    $assoTypeIdentifiers = explode(',', config("seeder.asso.type.identifiers"));
    $assoParents = explode(',', config("seeder.asso.parents"));
    return [
        'id' => Uuid::generate()->string,
        'type_id' => AssoType::where('type', $faker->randomElement($assoTypeIdentifiers))->first()->id,
        'parent_id' => Asso::where('login', $faker->randomElement($assoParents))->first()->id,
        'login' => $faker->regexify('\w{0,15}'),
        'shortname' => $faker->company(),
        'name' => $faker->catchPhrase(),
        'image' => 'todo',
        'description' => $faker->realText(400, 2),
        'in_cemetery_at' => $faker->boolean(20) ? $faker->dateTimeBetween('-5 years', 'now') : null,
   ];
});
