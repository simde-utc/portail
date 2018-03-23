<?php

use Illuminate\Database\Seeder;
use App\Models\Asso;
use App\Models\AssoType;

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
				'name' => 'BDE',
				'login' => 'bde',
				'description' => 'Bureau des étudiants',
				'type_asso_id' => '1901',
			],
			[
				'name' => 'SiMDE',
				'login' => 'simde',
				'description' => 'Service Informatique de la MDE',
				'type_asso_id' => 'commission',
				'parent_id' => 'bde',
			]
  	    ];

        foreach ($assos as $asso) {
			Asso::create([
				'name' => $asso['name'],
				'login' => $asso['login'],
				'description' => $asso['description'],
				'type_asso_id' => isset($asso['type_asso_id']) ? AssoType::where('name', $asso['type_asso_id'])->first()->id : null,
				'parent_id' => isset($asso['parent_id']) ? Asso::where('login', $asso['parent_id'])->first()->id : null,
			]);
      	}
    }
}
