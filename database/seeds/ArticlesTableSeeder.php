<?php

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\ArticleAction;
use App\Models\Visibility;
use App\Models\Asso;
use App\Models\User;

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
        		'title' => 'Samy a tout cassé !!!',
		        'content' => 'Le serveur des associations a été cassé par Samy ce jour. Paix à lui (le serveur pas Samy)',
                'description' => 'Samy est encore un expert en informatique, n\'hésitez pas à voir pourquoi en lisant l\'article',
                'created_by' => Asso::findByLogin('simde'),
		        'owner' => Asso::findByLogin('simde'),
		        'visibility_id' => 'public',
                'actions' => [
                    [
                        'user_id' => User::where('firstname', 'Samy')->first()->id,
                        'key' => 'liked',
                        'value' => false,
                    ],
                    [
                        'user_id' => User::where('firstname', 'Samy')->first()->id,
                        'key' => 'seen',
                        'value' => 3,
                    ],
                    [
                        'user_id' => User::where('firstname', 'Samy')->first()->id,
                        'key' => 'shared',
                        'value' => 1,
                    ],
                    [
                        'user_id' => User::where('firstname', 'Rémy')->first()->id,
                        'key' => 'liked',
                        'value' => true,
                    ],
                    [
                        'user_id' => User::where('firstname', 'Natan')->first()->id,
                        'key' => 'seen',
                        'value' => 40, // Big fan
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
                        'user_id' => User::where('firstname', 'Romain')->first()->id,
                        'key' => 'liked',
                        'value' => true,
                    ],
                ],
	        ],
		 [
	        	'title' => 'J\'ai',
		        'description' => 'Romain a besoin de seeds',
                'content' => 'Mais il n\'a pas d\'inspiration',
                'created_by' => Asso::findByLogin('simde'),
		        'owner' => Asso::findByLogin('simde'),
		        'visibility_id' => 'cas',
                'actions' => [
                    [
                        'user_id' => User::where('firstname', 'Romain')->first()->id,
                        'key' => 'liked',
                        'value' => true,
                    ],
                ],
	        ],
		 [
	        	'title' => 'Besoin',
		        'description' => 'Romain teste un scrollview prétendument "infini"',
                'content' => 'Mais il n\'a pas d\'inspiration',
                'created_by' => Asso::findByLogin('simde'),
		        'owner' => Asso::findByLogin('simde'),
		        'visibility_id' => 'cas',
                'actions' => [
                    [
                        'user_id' => User::where('firstname', 'Romain')->first()->id,
                        'key' => 'liked',
                        'value' => true,
                    ],
                ],
	        ],
		 [
	        	'title' => 'de',
		        'description' => 'Romain doit donc créer beaucoup d\'articles',
                'content' => 'Mais il n\'a pas d\'inspiration',
                'created_by' => Asso::findByLogin('simde'),
		        'owner' => Asso::findByLogin('simde'),
		        'visibility_id' => 'cas',
                'actions' => [
                    [
                        'user_id' => User::where('firstname', 'Romain')->first()->id,
                        'key' => 'liked',
                        'value' => true,
                    ],
                ],
	        ],
		 [
	        	'title' => 'seeds',
		        'description' => 'Romain veut remplir tout l\'écran',
                'content' => 'Mais il n\'a pas d\'inspiration',
                'created_by' => Asso::findByLogin('simde'),
		        'owner' => Asso::findByLogin('simde'),
		        'visibility_id' => 'cas',
                'actions' => [
                    [
                        'user_id' => User::where('firstname', 'Romain')->first()->id,
                        'key' => 'liked',
                        'value' => true,
                    ],
                ],
	        ],
		 [
	        	'title' => 'seeds',
		        'description' => 'Romain est parfois un kiwi',
                'content' => 'Mais il n\'a pas d\'inspiration',
                'created_by' => Asso::findByLogin('simde'),
		        'owner' => Asso::findByLogin('simde'),
		        'visibility_id' => 'cas',
                'actions' => [
                    [
                        'user_id' => User::where('firstname', 'Romain')->first()->id,
                        'key' => 'liked',
                        'value' => true,
                    ],
                ],
	        ],
		 [
	        	'title' => 'seeds',
		        'description' => 'Romain est dans la lune',
                'content' => 'Mais pourtant il n\'a pas d\'inspiration',
                'created_by' => Asso::findByLogin('simde'),
		        'owner' => Asso::findByLogin('simde'),
		        'visibility_id' => 'cas',
                'actions' => [
                    [
                        'user_id' => User::where('firstname', 'Romain')->first()->id,
                        'key' => 'liked',
                        'value' => true,
                    ],
                ],
	        ],
	        [
	        	'title' => 'Grand spectacle du PAE',
		        'content' => 'Ce jeudi, les associations du PAE ont eu l\'honneur de présenter devant plus de 500 UTCéens un grand spectacle...',
                'created_by' => Asso::findByLogin('pae'),
		        'owner' => Asso::findByLogin('pae'),
		        'visibility_id' => 'contributorBde',
                'actions' => [
                    [
                        'user_id' => User::where('firstname', 'Rémy')->first()->id,
                        'key' => 'liked',
                        'value' => true,
                    ],
                    [
                        'user_id' => User::where('firstname', 'Samy')->first()->id,
                        'key' => 'seen',
                        'value' => 2,
                    ],
                ],
	        ]
        ];

        foreach ($articles as $article) {
        	$model = Article::create([
        		'title'           => $article['title'],
		        'content'         => $article['content'],
		        'visibility_id'   => Visibility::where('type', $article['visibility_id'])->first()->id,
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

    }
}
