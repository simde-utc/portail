<?php

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Visibility;
use App\Models\User;
use App\Models\Comment;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $comments = [
            [
                'body' => 'Vraiment une équipe de choc, gg.',
                'user_id' => User::find(1)->id,
                'visibility_id' => Visibility::where('type', 'public')->first()->id,
                'commentable_id' => Article::find(1)->id,
                'commentable_type' => Article::class
            ],
            [
                'body' => 'Un autre comment.',
                'user_id' => User::find(1)->id,
                'visibility_id' => Visibility::where('type', 'public')->first()->id,
                'commentable_id' => Article::find(2)->id,
                'commentable_type' => Article::class
            ],
            [
                'body' => 'Une réponse à un autre comment.',
                'parent_id' => 2,
                'user_id' => User::find(3)->id,
                'visibility_id' => Visibility::where('type', 'public')->first()->id,
                'commentable_id' => Article::find(2)->id,
                'commentable_type' => Article::class
            ],
        ];

        foreach ($comments as $comment => $values)
            Comment::create($values);
    }
}
