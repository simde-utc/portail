<?php

use Illuminate\Database\Seeder;
use App\Models\AssoType;
use App\Models\Asso;
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
                'name'          => 'PVDC',
                'login'         => 'pvdc',
                'description'   => 'Pole Vie de Campus',
                'type_asso_id'  => AssoType::where('name', '1901')->first()->id,
            ],
            [
                'name'          => 'PAE',
                'login'         => 'pae',
                'description'   => 'Pole Artistique et Evenementiel',
                'type_asso_id'  => AssoType::where('name', '1901')->first()->id,
            ],
            [
                'name'          => 'BDE',
                'login'         => 'bde',
                'description'   => 'Bureau des Etudiants',
                'type_asso_id'  => AssoType::where('name', '1901')->first()->id,
            ],
            [
                'name'          => 'Integration UTC',
                'login'         => 'integ',
                'description'   => 'La meilleure des assos <3',
                'type_asso_id'  => AssoType::where('name', 'commission')->first()->id,
                //'parent_id' => Asso::where('name', 'PVDC')->first()->id,
            ],
        ];

        foreach ($assos as $asso => $values){
            Asso::create($values);
        }

    }
}
