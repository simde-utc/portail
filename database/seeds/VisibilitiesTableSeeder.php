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
            ],
            [
                'type' => 'logged',
                'name' => 'Toute personne connectée',
            ],
            [
                'type' => 'cas',
                'name' => 'Toute personne connectée au CAS',
            ],
            [
                'type' => 'contributor',
                'name' => 'Tout cotisant BDE-UTC',
            ],
            [
                'type' => 'private',
                'name' => 'Privée aux membres',
            ],
            [
                'type' => 'owner',
                'name' => 'Uniquement la personne créatrice',
            ],
        ];

        foreach ($visibilities as $key => $visibility) {
            $id = Visibility::create($visibility)->id;

			if ($key !== 0)
				Visibility::where('type', $visibilities[$key - 1]['type'])->update(['parent_id' => $id]);
        }
    }
}
