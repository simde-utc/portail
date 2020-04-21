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
                'created_by' => User::where('email', config('app.admin.email'))->first(),
                'owned_by' => Article::get()[0],
            ],
            [
                'body' => 'Et j\'en suis fier #KIKOO JE SPAM',
                'created_by' => User::where('email', config('app.admin.email'))->first(),
                'owned_by' => Article::get()[1],
            ],
            [
                'body' => 'Une réponse à un autre comment.',
                'created_by' => User::where('email', 'natan.danous@etu.utc.fr')->first(),
            ],
        ];

        foreach ($comments as $comment) {
            $model = Comment::create([
                'body' => $comment['body'],
                'created_by_id' => $comment['created_by']->id,
                'created_by_type' => get_class($comment['created_by']),
            ]);

            $model->changeOwnerTo(($comment['owned_by'] ?? $lastComment ?? null))->save();
            $lastComment = $model;
        }
    }
}
