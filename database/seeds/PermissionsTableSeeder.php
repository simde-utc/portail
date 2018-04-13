<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

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
				'name' => 'superadmin',
				'description' => 'Droit Super administrateur',
				'limited_at' => 1,
				'only_system' => true,
			],
			[
				'name' => 'admin',
				'description' => 'Droit administrateur',
				'only_system' => true,
			],
			[
				'name' => 'membres',
				'description' => 'Gestion des membres',
			],
			[
				'name' => 'tresorie',
				'description' => 'Gestion de la trésorie',
			],
		];

		foreach ($permissions as $permission) {
			Permission::create($permission);
		}
    }
}
