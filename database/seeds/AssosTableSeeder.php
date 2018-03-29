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
		        'name'          => 'BDE',
		        'login'         => 'bde',
		        'description'   => 'Bureau des Etudiants',
		        'type_asso_id'  => '1901',
	        ],
	        [
                'name'          => 'PVDC',
                'login'         => 'pvdc',
                'description'   => 'Pôle Vie de Campus',
                'type_asso_id'  => '1901',
		        'parent_id'     => 'bde',
            ],
            [
                'name'          => 'PAE',
                'login'         => 'pae',
                'description'   => 'Pôle Artistique et Événementiel',
                'type_asso_id'  => '1901',
	            'parent_id'     => 'bde',
            ],
            [
                'name'          => 'Integration UTC',
                'login'         => 'integ',
                'description'   => 'La meilleure des assos <3',
                'type_asso_id'  => 'commission',
                'parent_id'     => 'pvdc',
            ],
        ];

        foreach ($assos as $asso){
            Asso::create([
            	'name' => $asso['name'],
	            'login' => $asso['login'],
	            'description' => $asso['description'],
	            'type_asso_id' => AssoType::where('name', $asso['type_asso_id'])->first()->id,
	            'parent_id' => isset($asso['parent_id']) ? Asso::where('login', $asso['parent_id'])->first()->id : null,
            ]);
        }

    }
}
