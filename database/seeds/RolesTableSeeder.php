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
					'user',
					'asso',
					'group',
                    'client',
				]
			],
			[
				'type' => config('portail.roles.admin.users'),
				'name' => 'Administrateur',
				'description' => 'Personne ayant tous les droits sur le serveur',
				'parents' => [
					'superadmin',
				],
				'permissions' => [
					'user',
					'asso',
					'group',
                    'client',
				]
			],
			[
				'type' => config('portail.roles.admin.assos'),
				'name' => 'Président',
				'description' => 'Responsable d\'une organisation',
				'limited_at' => 1,
				'only_for' => 'assos',
				'permissions' => [
					'asso_treasury',
                    'asso_ticketing',
                    'asso_calendar',
                    'asso_event',
                    'asso_contact',
                    'asso_article',
                    'asso_data',
				]
			],
			[
				'type' => 'vice-president',
				'name' => 'Vice-Président',
				'description' => 'Co-responsable d\'une organisation',
				'limited_at' => 4,
				'only_for' => 'assos',
				'parents' => [
					config('portail.roles.admin.assos'),
				],
				'permissions' => [
					'asso_treasury',
                    'asso_ticketing',
                    'asso_calendar',
                    'asso_event',
                    'asso_contact',
                    'asso_article',
                    'asso_data',
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
                'permissions' => [
                    'asso_calendar',
                    'asso_event',
                    'asso_contact',
                    'asso_article',
                    'asso_data',
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
				'permissions' => [
                    'asso_calendar',
                    'asso_event',
                    'asso_contact',
                    'asso_article',
                    'asso_data',
				],
			],
			[
				'type' => 'treasury',
				'name' => 'Trésorier',
				'description' => 'Responsable de la trésorie',
				'limited_at' => 1,
				'only_for' => 'assos',
				'parents' => [
					'vice-president',
				],
				'permissions' => [
					'asso_treasury',
                    'asso_event',
				]
			],
			[
				'type' => 'vice-treasury',
				'name' => 'Vice-Trésorier',
				'description' => 'Co-responsable de la trésorie',
				'limited_at' => 4,
				'only_for' => 'assos',
				'parents' => [
                    'treasury',
				],
				'permissions' => [
					'asso_treasury',
                    'asso_event',
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
                    'treasury',
					'vice-treasury',
				],
				'permissions' => [
                    'asso_event',
				]
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
				'permissions' => [
                    'asso_calendar',
                    'asso_event',
                    'asso_article'
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
				'permissions' => [
                    'asso_event',
                    'asso_article',
                    'asso_data',
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
				'permissions' => [
                    'asso_event',
				],
			],
			[
				'type' => 'animation',
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
				'permissions' => [
                    'asso_event',
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
				'permissions' => [
                    'asso_event',
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
				'permissions' => [
                    'asso_event',
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
				'description' => 'Administrateur du group',
				'limited_at' => 1,
				'only_for' => 'groups',
				'permissions' => [
                    'group_member',
                    'group_calendar',
                    'group_event',
                    'group_contact',
                    'group_article',
				],
			],
			[
				'type' => 'group planner',
				'name' => 'Planificateur',
				'description' => 'Personne planifiant les évènements et les calendriers du groupe',
				'only_for' => 'groups',
				'permissions' => [
                    'group_calendar',
                    'group_event',
				],
			],
			[
				'type' => 'group writer',
				'name' => 'Ecrivain',
				'description' => 'Personne écrivant les articles du groupe',
				'only_for' => 'groups',
				'permissions' => [
                    'group_article',
				],
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
