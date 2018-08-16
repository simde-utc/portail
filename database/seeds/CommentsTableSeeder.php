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
                'user_id' => User::where('firstname', 'Samy')->first()->id,
                'visibility_id' => Visibility::where('type', 'public')->first()->id,
                'commentable_id' => Article::get()[0]->id,
                'commentable_type' => Article::class
            ],
            [
                'body' => 'Un autre comment.',
                'user_id' => User::where('firstname', 'Samy')->first()->id,
                'visibility_id' => Visibility::where('type', 'public')->first()->id,
                'commentable_id' => Article::get()[1]->id,
                'commentable_type' => Article::class
            ],
            [
                'body' => 'Une réponse à un autre comment.',
                'parent_id' => 1,
                'user_id' => User::where('firstname', 'Natan')->first()->id,
                'visibility_id' => Visibility::where('type', 'public')->first()->id,
                'commentable_id' => Article::get()[1]->id,
                'commentable_type' => Article::class
            ],
        ];

        foreach ($comments as $comment => $values)
            Comment::create([
                'body' => $values['body'],
                'user_id' => $values['user_id'],
                'visibility_id' => $values['visibility_id'],
                'commentable_id' => $values['commentable_id'],
                'commentable_type' => $values['commentable_type'],
                'parent_id' => isset($values['parent_id']) ? Comment::get()[$values['parent_id']]->id : null
            ]);
    }
}
