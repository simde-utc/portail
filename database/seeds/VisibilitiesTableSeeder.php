<?php

use Illuminate\Database\Seeder;
use App\Models\Visibility;

class VisibilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Visibilités possibles, du moins permissif au plus permissif
        $visibilities = [
            [
                'type' => 'public',
                'name' => 'Public',
				'childs' => [
					'logged',
				],
            ],
            [
                'type' => 'logged',
                'name' => 'Toute personne connectée',
				'childs' => [
					'casOrWasCas',
					'private',
				],
            ],
            [
                'type' => 'casOrWasCas',
                'name' => 'Toute personne connectée au CAS ou maintenant Tremplin',
				'childs' => [
					'cas',
					'private',
				],
            ],
            [
                'type' => 'cas',
                'name' => 'Toute personne connectée au CAS',
				'childs' => [
					'personnal',
					'student',
					'private',
				],
            ],
            [
                'type' => 'student',
                'name' => 'Etudiant',
				'childs' => [
                    'studentUtc',
                    'studentEscom',
					'private',
				],
            ],
            [
                'type' => 'studentUtc',
                'name' => 'Etudiant UTC',
				'childs' => [
					'contributorBde',
					'private',
				],
            ],
            [
                'type' => 'studentEscom',
                'name' => 'Etudiant ESCOM',
				'childs' => [
					'contributorBde',
					'private',
				],
            ],
            [
                'type' => 'personnal',
                'name' => 'Personnel UTC',
				'childs' => [
					'contributorBde',
					'private',
				],
            ],
            [
                'type' => 'contributorBde',
                'name' => 'Tout cotisant BDE-UTC',
				'childs' => [
					'private',
				],
            ],
            [
                'type' => 'private',
                'name' => 'Privée aux membres',
				'childs' => [
					'owner',
				],
            ],
            [
                'type' => 'owner',
                'name' => 'Uniquement la personne créatrice',
				'childs' => [
					'internal',
				],
            ],
            [
                'type' => 'internal',
                'name' => 'Réservé à la gestion interne du système',
            ],
        ];

        foreach ($visibilities as $visibility) {
            Visibility::create([
				'type' => $visibility['type'],
				'name' => $visibility['name'],
			]);
        }

        foreach ($visibilities as $visibility) {
            if (isset($visibility['childs']))
				Visibility::findByType($visibility['type'])->childs()->attach(Visibility::whereIn('type', $visibility['childs'])->get());
        }
    }
}
