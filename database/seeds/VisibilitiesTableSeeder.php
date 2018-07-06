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
    public function run() : void
    {
        // Visibilités possibles, du moins permissif au plus permissif
        $visibilities = [
            [
                'type' => 'public',
                'name' => 'Public',
            ],
            [
                'type' => 'logged',
                'name' => 'Toute personne connectée',
                'parent' => 'public',
            ],
            [
                'type' => 'cas',
                'name' => 'Toute personne connectée au CAS',
                'parent' => 'logged',
            ],
            [
                'type' => 'contributorBde',
                'name' => 'Tout cotisant BDE-UTC',
                'parent' => 'cas',
            ],
            [
                'type' => 'private',
                'name' => 'Privée aux membres',
                'parent' => 'contributorBDE',
            ],
            [
                'type' => 'owner',
                'name' => 'Uniquement la personne créatrice',
                'parent' => 'private',
            ],
            [
                'type' => 'internal',
                'name' => 'Réservé à la gestion interne du système',
                'parent' => 'owner',
            ],
        ];

        foreach ($visibilities as $visibility) {
            Visibility::create([
				'type' => $visibility['type'],
				'name' => $visibility['name'],
                'parent_id' => Visibility::where('type', $visibility['parent'] ?? null)->first()->id ?? null,
			]);
        }
    }
}
