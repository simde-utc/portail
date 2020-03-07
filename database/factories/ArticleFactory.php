<?php
/**
 * Article factory
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2020, SiMDE-UTC
 * @license GNU GPL-3.0
 */

use App\Models\Article;
use App\Models\Visibility;
use App\Models\User;
use App\Models\Asso;
use Webpatser\Uuid\Uuid;
use Faker\Generator as Faker;

$factory->define(Article::class, function (Faker $faker) {

    $userArticle = $faker->boolean(50);
    $createdAndOwnedById = $userArticle ? $faker->randomElement(User::all()->toArray())['id'] : $faker->randomElement(Asso::all()->toArray())['id'];
    $createdAndOwnedByType = $userArticle ? "App\\Models\\User" : "App\\Models\\Asso" ;
    
    return [
        'id' => Uuid::generate()->string,
        'title' => $faker->sentence(6, true),
        'description' => $faker->realText(200, 2),
        'content' => $faker->realText(1000, 2),
        'image' => config('seeder.article.generate_images') ? '/images/articles/'.$faker->image('public/images/articles', config('seeder.article.image_width'), config('seeder.article.image_height'), null, false) : null,
        'visibility_id' => $faker->randomElement(Visibility::all()->toArray())['id'],
        'event_id' => null,
        'created_by_id' => $createdAndOwnedById,
        'created_by_type' => $createdAndOwnedByType,
        'owned_by_id' => $createdAndOwnedById,
        'owned_by_type' => $createdAndOwnedByType,
    ];
});
