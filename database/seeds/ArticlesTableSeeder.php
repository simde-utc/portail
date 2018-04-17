<?php

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Visibility;
use App\Models\Asso;

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
		        'asso' => 'simde',
		        'visibility_id' => 'public',
	        ],
	        [
	        	'title' => 'L\'intégration va commencer !',
		        'content' => 'Début de l\'intégration le jeudi 30 août 2018',
		        'asso' => 'integ',
		        'visibility_id' => 'cas',
	        ],
	        [
	        	'title' => 'Grand spectacle du PAE',
		        'content' => 'Jeudi dernier, les associations du PAE ont eu l\'honneur de présenter devant plus de 500 UTCéens un grand spectacle...',
		        'asso' => 'pae',
		        'visibility_id' => 'contributor',
	        ]
        ];

        foreach ($articles as $article) {
        	Article::create([
        		'title' => $article['title'],
		        'content' => $article['content'],
		        'asso_id' => Asso::where('login', $article['asso'])->first()->id,
		        'visibility_id' => Visibility::where('type', $article['visibility_id'])->first()->id,
	        ]);
        }

    }
}
