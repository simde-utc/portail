<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$roles = [
			[
				'name' => 'superadmin',
				'description' => 'Super administrateur',
				'limited_at' => 1,
				'only_system' => true,
				'permissions' => [
					'membres',
					'tresorie',
				]
			],
			[
				'name' => 'admin',
				'description' => 'Administrateur',
				'only_system' => true,
				'permissions' => [
					'membres',
					'tresorie',
				]
			],
			[
				'name' => 'president',
				'description' => 'Président',
				'limited_at' => 1,
				'permissions' => [
					'membres',
					'tresorie',
				]
			],
			[
				'name' => 'vice-president',
				'description' => 'Vice-Président',
				'limited_at' => 1,
				'parent_id' => 'president',
				'permissions' => [
					'membres',
					'tresorie',
				]
			],
			[
				'name' => 'secretaire general',
				'description' => 'Secrétaire Général',
				'limited_at' => 1,
				'parent_id' => 'vice-president',
				'permissions' => [
					'membres',
				]
			],
			[
				'name' => 'tresorier',
				'description' => 'Trésorier',
				'limited_at' => 1,
				'parent_id' => 'secretaire general',
				'permissions' => [
					'tresorie',
				]
			],
			[
				'name' => 'bureau',
				'description' => 'Membre du bureau',
				'parent_id' => 'tresorier',
				'permissions' => [

				]
			],
		];

		foreach ($roles as $role) {
			Role::create([
				'name' => $role['name'],
				'description' => $role['description'],
				'limited_at' => $role['limited_at'] ?? null,
				'only_system' => $role['only_system'] ?? false,
				'parent_id' => Role::where([
	              'name' => $role['parent_id'] ?? null
	            ])->first()->id ?? null
 			])->givePermissionTo($role['permissions'] ?? []);
		}
    }
}
