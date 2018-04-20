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
					'superadmin',
					'admin',
				]
			],
			[
				'type' => 'admin',
				'name' => 'Administrateur',
				'description' => 'Personne ayant tous les droits sur le serveur',
				'parents' => [
					'superadmin',
				],
				'permissions' => [
					'admin',
				]
			],
			[
				'type' => 'president',
				'name' => 'Président',
				'description' => 'Responsable d\'une organisation',
				'limited_at' => 1,
				'only_for' => 'assos',
				'permissions' => [
					'tresorie',
					'bureau',
				]
			],
			[
				'type' => 'vice-president',
				'name' => 'Vice-Président',
				'description' => 'Co-responsable d\'une organisation',
				'limited_at' => 1,
				'only_for' => 'assos',
				'parents' => [
					'president',
				],
				'permissions' => [
					'tresorie',
					'bureau',
				]
			],
			[
				'type' => 'secretaire general',
				'name' => 'Secrétaire Général',
				'description' => 'Administrateur de l\'organisation',
				'limited_at' => 1,
				'only_for' => 'assos',
				'parents' => [
					'president',
					'vice-president',
				],
				'permissions' => [
					'bureau',
				]
			],
			[
				'type' => 'tresorier',
				'name' => 'Trésorier',
				'description' => 'Responsable de la trésorie',
				'limited_at' => 1,
				'only_for' => 'assos',
				'parents' => [
					'president',
					'vice-president',
					'secretaire general',
				],
				'permissions' => [
					'tresorie',
					'bureau',
				]
			],
			[
				'type' => 'bureau',
				'name' => 'Bureau',
				'description' => 'Membre du bureau',
				'only_for' => 'assos',
				'parents' => [
					'president',
					'vice-president',
					'secretaire general',
					'tresorier',
				],
				'permissions' => [
					'bureau',
				]
			],
			[
				'type' => 'resp info',
				'name' => 'Responsable Informatique',
				'description' => 'Responsable informatique de l\'association',
				'only_for' => 'assos',
				'parents' => [
					'president',
					'vice-president',
					'secretaire general',
					'tresorier',
					'bureau',
				],
				'permissions' => [
					'bureau',
				]
			],
			[
				'type' => 'developer',
				'name' => 'Développeur',
				'description' => 'Fais parti de l\'équipe informatique de l\'association',
				'only_for' => 'assos',
				'parents' => [
					'president',
					'vice-president',
					'secretaire general',
					'tresorier',
					'bureau',
					'resp info',
				],
			],
			[
				'type' => 'group admin',
				'name' => 'Administrateur',
				'description' => 'Administrateur du groupe',
				'limited_at' => 1,
				'only_for' => 'groups',
			],
		];

		foreach ($roles as $role) {
			Role::create([
				'type' => $role['type'],
				'name' => $role['name'],
				'description' => $role['description'],
				'limited_at' => $role['limited_at'] ?? null,
				'only_for' => $role['only_for'] ?? 'users',
 			])->givePermissionTo($role['permissions'] ?? [])
				->assignParentRole($role['parents'] ?? []);
		}
    }
}
