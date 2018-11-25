<?php

use Illuminate\Database\Seeder;
use App\Models\Partner;

class PartnersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $partners = [
            [
                'name' => 'Société Générale',
                'description' => 'La société générale est une banque (surprise). Elle donne des sous au bde et aux assos',
                'image' => 'image de la sogé',
            ],
            [
                'name' => 'Ecocup',
                'description' => 'Ecocup est la référence en gobelet réutilisables pour les associations de l\'UTC.',
                'image' => 'image de ecocup',
            ],
        ];

        foreach ($partners as $partner) {
            Partner::create($partner);
        }

    }
}
