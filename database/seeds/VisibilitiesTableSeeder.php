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
                'type' => 'active',
                'name' => 'Toute personne connectée',
                'parent' => 'public',
            ],
            [
                'type' => 'cas',
                'name' => 'Toute personne connectée au CAS',
                'parent' => 'active',
            ],
            [
                'type' => 'contributorBde',
                'name' => 'Tout cotisant BDE-UTC',
                'parent' => 'cas',
            ],
            [
                'type' => 'private',
                'name' => 'Privée',
                'parent' => 'contributorBde',
            ],
            [
                'type' => 'internal',
                'name' => 'Réservé à la gestion interne du système',
                'parent' => 'private',
            ],
        ];

        foreach ($visibilities as $visibility) {
            Visibility::create([
                'type' => $visibility['type'],
                'name' => $visibility['name'],
                'parent_id' => (Visibility::findByType(($visibility['parent'] ?? null))->first()->id ?? null),
            ]);
        }
    }
}
