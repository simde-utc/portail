<?php

use Illuminate\Database\Seeder;
use App\Models\AssoType;
use App\Models\App;

class AssosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $assos = [
            [
                'name'       => 'PVDC',
                'description'   => 'Pole Vie de Campus',
                'type_asso_id'  => AssoType::find(1)->id,
            ],
            [
                'name'       => 'PAE',
                'description'   => 'Pole Artistique et Evenementiel',
                'type_asso_id'  => AssoType::find(1)->id,
            ],
            [
                'name'       => 'BDE',
                'description'   => 'Bureau des Etudiants',
                'type_asso_id'  => AssoType::find(1)->id,
            ],
            [
                'name'       => 'Integration UTC',
                'description'   => 'La meilleure des assos <3',
                'type_asso_id'  => AssoType::find(2)->id,
                'parent_id' => Asso::find(1)->id,
            ],
        ];

        foreach ($assos as $asso => $values){
            Asso::create($values);
        }

    }
}
