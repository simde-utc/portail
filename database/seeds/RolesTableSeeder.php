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
				]
			],
			[
				'type' => 'vice-president',
				'name' => 'Vice-Président',
				'description' => 'Co-responsable d\'une organisation',
				'limited_at' => 4,
				'only_for' => 'assos',
				'parents' => [
					'president',
				],
				'permissions' => [
					'tresorie',
				]
			],
			[
				'type' => 'secretaire general',
				'name' => 'Secrétaire Général',
				'description' => 'Administrateur de l\'organisation',
				'limited_at' => 1,
				'only_for' => 'assos',
				'parents' => [
					'vice-president',
				],
			],
			[
				'type' => 'vice-secretaire',
				'name' => 'Vice-Secrétaire',
				'description' => 'Adjoint du secrétaire',
				'limited_at' => 4,
				'only_for' => 'assos',
				'parents' => [
					'secretaire general',
				],
			],
			[
				'type' => 'tresorier',
				'name' => 'Trésorier',
				'description' => 'Responsable de la trésorie',
				'limited_at' => 1,
				'only_for' => 'assos',
				'parents' => [
					'vice-president',
				],
				'permissions' => [
					'tresorie',
				]
			],
			[
				'type' => 'vice-tresorier',
				'name' => 'Vice-Trésorier',
				'description' => 'Co-responsable de la trésorie',
				'limited_at' => 4,
				'only_for' => 'assos',
				'parents' => [
                    'tresorier',
				],
				'permissions' => [
					'tresorie',
				]
			],
			[
				'type' => 'bureau',
				'name' => 'Bureau',
				'description' => 'Membre du bureau',
				'only_for' => 'assos',
				'parents' => [
					'vice-president',
                    'secretaire general',
					'vice-secretaire',
                    'tresorier',
					'vice-tresorier',
				],
			],
			[
				'type' => 'resp informatique',
				'name' => 'Responsable Informatique',
				'description' => 'Responsable informatique de l\'association',
                'limited_at' => 1,
				'only_for' => 'assos',
				'parents' => [
					'bureau',
				],
			],
			[
				'type' => 'developer',
				'name' => 'Développeur',
				'description' => 'Membre de l\'équipe informatique de l\'association',
				'only_for' => 'assos',
				'parents' => [
					'resp informatique',
				],
			],
			[
				'type' => 'resp communication',
				'name' => 'Responsable Communication',
				'description' => 'Responsable communication de l\'association',
                'limited_at' => 1,
				'only_for' => 'assos',
				'parents' => [
					'bureau',
				],
			],
			[
				'type' => 'communication',
				'name' => 'Chargé de communication',
				'description' => 'Membre de l\'équipe communication de l\'association',
				'only_for' => 'assos',
				'parents' => [
					'resp communication',
				],
			],
			[
				'type' => 'resp animation',
				'name' => 'Responsable animation',
				'description' => 'Responsable animation de l\'association',
                'limited_at' => 1,
				'only_for' => 'assos',
				'parents' => [
					'bureau',
				],
			],
			[
				'type' => 'annimation',
				'name' => 'Chargé de l\'animation',
				'description' => 'Membre de l\'équipe animation de l\'association',
				'only_for' => 'assos',
				'parents' => [
					'resp animation',
				],
			],
			[
				'type' => 'resp partenariat',
				'name' => 'Responsable partenariat',
				'description' => 'Responsable partenariat de l\'association',
                'limited_at' => 1,
				'only_for' => 'assos',
				'parents' => [
					'bureau',
				],
			],
			[
				'type' => 'partenariat',
				'name' => 'Chargé du partenariat',
				'description' => 'Membre de l\'équipe partenariat de l\'association',
				'only_for' => 'assos',
				'parents' => [
					'resp partenariat',
				],
			],
			[
				'type' => 'resp logistique',
				'name' => 'Responsable logistique',
				'description' => 'Responsable logistique de l\'association',
                'limited_at' => 1,
				'only_for' => 'assos',
				'parents' => [
					'bureau',
				],
			],
			[
				'type' => 'logistique',
				'name' => 'Chargé de la logistique',
				'description' => 'Membre de l\'équipe logistique de l\'association',
				'only_for' => 'assos',
				'parents' => [
					'resp logistique',
				],
			],
			[
				'type' => 'resp',
				'name' => 'Responsable',
				'description' => 'Responsable dans l\'association',
				'only_for' => 'assos',
				'parents' => [
					'bureau',
				],
			],
			[
				'type' => 'membre',
				'name' => 'Membre de l\'association',
				'description' => 'Membre de l\'équipe associative',
				'only_for' => 'assos',
				'parents' => [
					'resp',
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
