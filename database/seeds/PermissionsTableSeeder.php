<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$permissions = [
			[
				'type' => 'superadmin',
				'name' => 'Droit Super administrateur',
				'description' => '',
				'limited_at' => 1,
				'is_system' => true,
			],
			[
				'type' => 'admin',
				'name' => 'Droit administrateur',
				'description' => '',
				'is_system' => true,
			],
			[
				'type' => 'membres',
				'name' => 'Membres',
				'description' => 'Gestion des membres',
			],
			[
				'type' => 'tresorie',
				'name' => 'Trésorie',
				'description' => 'Gestion de la trésorie',
			],
			[
				'type' => 'bureau',
				'name' => 'Bureau',
				'description' => 'Indique que la personne fait partie du bureau',
			],
		];

		foreach ($permissions as $permission) {
			Permission::create($permission);
		}
    }
}
