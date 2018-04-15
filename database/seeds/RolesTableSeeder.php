<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

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
				'type' => 'superadmin',
				'name' => 'Super administrateur',
				'description' => 'Personne ayant réellement tous les droits sur le service',
				'limited_at' => 1,
				'permissions' => [
					'admin',
					'tresorie',
				]
			],
			[
				'type' => 'admin',
				'name' => 'Administrateur',
				'description' => 'Personne ayant tous les droits sur le serveur',
				'permissions' => [
					'membres',
					'tresorie',
				]
			],
			[
				'type' => 'president',
				'name' => 'Président',
				'description' => 'Responsable d\'une organisation',
				'limited_at' => 1,
				'only_for' => 'assos_members',
				'permissions' => [
					'membres',
					'tresorie',
				]
			],
			[
				'type' => 'vice-president',
				'name' => 'Vice-Président',
				'description' => 'Co-responsable d\'une organisation',
				'limited_at' => 1,
				'only_for' => 'assos_members',
				'parent_id' => 'president',
				'permissions' => [
					'membres',
					'tresorie',
					'bureau',
				]
			],
			[
				'type' => 'secretaire general',
				'name' => 'Secrétaire Général',
				'description' => 'Administrateur de l\'organisation',
				'limited_at' => 1,
				'only_for' => 'assos_members',
				'parent_id' => 'vice-president',
				'permissions' => [
					'membres',
					'bureau',
				]
			],
			[
				'type' => 'tresorier',
				'name' => 'Trésorier',
				'description' => 'Responsable de la trésorie',
				'limited_at' => 1,
				'only_for' => 'assos_members',
				'parent_id' => 'secretaire general',
				'permissions' => [
					'tresorie',
					'bureau',
				]
			],
			[
				'type' => 'bureau',
				'name' => 'Bureau',
				'description' => 'Membre du bureau',
				'only_for' => 'assos_members',
				'parent_id' => 'tresorier',
				'permissions' => [
					'bureau',
				]
			],
		];

		foreach ($roles as $role) {
			Role::create([
				'type' => $role['type'],
				'name' => $role['name'],
				'description' => $role['description'],
				'limited_at' => $role['limited_at'] ?? null,
				'only_for' => $role['only_for'] ?? 'users',
				'parent_id' => Role::where([
	              'name' => $role['parent_id'] ?? null
	            ])->first()->id ?? null
 			])->givePermissionTo(Permission::whereIn('type', $role['permissions'] ?? [])->pluck('id')->toArray());
		}
    }
}
