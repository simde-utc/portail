<?php

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\ArticleAction;
use App\Models\Visibility;
use App\Models\Asso;
use App\Models\User;
use PHPStan\DependencyInjection\ConditionalTagsExtension;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $articles = [
            [
                'title' => 'Samy a créé le Portail !!!',
                'content' => 'Le serveur des associations a été intiié par Samy ce jour. Paix sur lui (Samy, pas le serveur)',
                'description' => 'Samy est encore un expert en informatique, n\'hésitez pas à voir pourquoi en lisant l\'article',
                'created_by' => Asso::findByLogin('simde'),
                'owner' => Asso::findByLogin('simde'),
                'visibility_id' => 'public',
                'actions' => [
                    [
                        'user_id' => User::where('email', config('app.admin.email'))->first()->id,
                        'key' => 'liked',
                        'value' => false,
                    ],
                    [
                        'user_id' => User::where('email', config('app.admin.email'))->first()->id,
                        'key' => 'seen',
                        'value' => 3,
                    ],
                    [
                        'user_id' => User::where('email', config('app.admin.email'))->first()->id,
                        'key' => 'shared',
                        'value' => 1,
                    ],
                    [
                        'user_id' => User::where('email', 'alexandre.brasseur@etu.utc.fr')->first()->id,
                        'key' => 'liked',
                        'value' => true,
                    ],
                    [
                        'user_id' => User::where('email', 'natan.danous@etu.utc.fr')->first()->id,
                        'key' => 'seen',
                        'value' => 40,
                    // Big fan
                    ]
                ],
            ],
            [
                'title' => 'L\'intégration va commencer !',
                'description' => 'Début de l\'intégration le jeudi 30 août 2018',
                'content' => 'Si tu veux predre ton pack integ blablablalbala',
                'created_by' => Asso::findByLogin('integ'),
                'owner' => Asso::findByLogin('integ'),
                'visibility_id' => 'cas',
                'actions' => [
                    [
                        'user_id' => User::where('email', 'romain.maliach-auguste@etu.utc.fr')->first()->id,
                        'key' => 'liked',
                        'value' => true,
                    ],
                ],
            ],
            [
                'title' => 'Grand spectacle du PAE',
                'content' => 'Ce jeudi, les associations du PAE ont eu l\'honneur de présenter devant plus de 500 UTCéens un grand spectacle...',
                'created_by' => Asso::findByLogin('poleae'),
                'owner' => Asso::findByLogin('poleae'),
                'visibility_id' => 'contributorBde',
                'actions' => [
                    [
                        'user_id' => User::where('email', 'alexandre.brasseur@etu.utc.fr')->first()->id,
                        'key' => 'liked',
                        'value' => true,
                    ],
                    [
                        'user_id' => User::where('email', config('app.admin.email'))->first()->id,
                        'key' => 'seen',
                        'value' => 2,
                    ],
                ],
            ]
        ];

        fprintf(STDOUT, "Seeding manually added articles\n");
        foreach ($articles as $article) {
            $model = Article::create([
                'title'           => $article['title'],
                'content'         => $article['content'],
                'visibility_id'   => Visibility::findByType($article['visibility_id'])->id,
                'created_by_id'   => isset($article['created_by']) ? $article['created_by']->id : null,
                'created_by_type' => isset($article['created_by']) ? get_class($article['created_by']) : null,
            ])->changeOwnerTo($article['owner']);

            $model->save();

            foreach ($article['actions'] as $action) {
                ArticleAction::create([
                    'article_id'    => $model->id,
                    'user_id'       => $action['user_id'],
                    'key'           => $action['key'],
                    'value'         => $action['value'],
                ]);
            }
        }

        fprintf(STDOUT, "Seeding random articles\n");
        $this->command->getOutput()->progressStart();

        for ($i = 0; $i < config('seeder.article.amount') ; $i++) {
            try {
                $article = factory(Article::class)->create()->save();
                $this->command->getOutput()->progressAdvance();
            } catch (\Throwable $th) {
                $i--;
            }
        }

        $this->command->getOutput()->progressFinish();

    }
}
