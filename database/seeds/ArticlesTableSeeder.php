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
                'created_by' => Asso::findByLogin('simde'),
		        'owner' => Asso::findByLogin('simde'),
                'collaborators' => [
                    Asso::findByLogin('bde'),
                ],
		        'visibility_id' => 'public',
	        ],
	        [
	        	'title' => 'L\'intégration va commencer !',
		        'content' => 'Début de l\'intégration le jeudi 30 août 2018',
                'created_by' => Asso::findByLogin('integ'),
		        'owner' => Asso::findByLogin('integ'),
                'collaborators' => [
                    Asso::findByLogin('bde'),
                    Asso::findByLogin('pvdc'),
                ],
		        'visibility_id' => 'cas',
	        ],
	        [
	        	'title' => 'Grand spectacle du PAE',
		        'content' => 'Ce jeudi, les associations du PAE ont eu l\'honneur de présenter devant plus de 500 UTCéens un grand spectacle...',
                'created_by' => Asso::findByLogin('pae'),
		        'owner' => Asso::findByLogin('pae'),
		        'visibility_id' => 'contributorBde',
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

            if (isset($article['collaborators'])) {
                foreach ($article['collaborators'] as $collaborator)
                    $model->collaborators()->attach($collaborator);
            }
        }

    }
}
