<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Asso;
use App\Models\Group;
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
        'owned_by' => new User,
				'permissions' => [
					'user',
					'asso',
					'group',
          'client',
          'service',
					'room',
          'role',
          'permission',
          'bobby',
				]
			],
			[
				'type' => config('portail.roles.admin.users'),
				'name' => 'Administrateur',
				'description' => 'Personne ayant tous les droits sur le serveur',
        'owned_by' => new User,
				'parents' => [
					'superadmin',
				],
				'permissions' => [
					'user',
					'asso',
					'group',
          'client',
          'service',
					'room',
          'role',
          'permission',
          'bobby',
				]
			],
			[
				'type' => config('portail.roles.admin.assos'),
				'name' => 'Président',
				'description' => 'Responsable d\'une organisation',
				'limited_at' => 1,
				'owned_by' => new Asso,
				'permissions' => [
					'treasury',
          'ticketing',
          'calendar',
          'event',
          'contact',
          'article',
          'comment',
          'data',
          'reservation',
          'role',
          'permission',
          'bobby',
				]
			],
			[
				'type' => 'vice-president',
				'name' => 'Vice-Président',
				'description' => 'Co-responsable d\'une organisation',
				'limited_at' => 4,
				'owned_by' => new Asso,
				'parents' => [
					config('portail.roles.admin.assos'),
				],
				'permissions' => [
					'treasury',
          'ticketing',
          'calendar',
          'event',
          'contact',
          'comment',
          'article',
          'data',
          'reservation',
          'role',
          'permission',
          'bobby',
				]
			],
			[
				'type' => 'secretaire general',
				'name' => 'Secrétaire Général',
				'description' => 'Administrateur de l\'organisation',
				'limited_at' => 1,
				'owned_by' => new Asso,
        'parents' => [
          'vice-president',
        ],
        'permissions' => [
          'calendar',
          'event',
          'contact',
          'article',
          'comment',
          'data',
          'reservation',
          'role',
          'permission',
				],
			],
			[
				'type' => 'vice-secretaire',
				'name' => 'Vice-Secrétaire',
				'description' => 'Adjoint du secrétaire',
				'limited_at' => 4,
				'owned_by' => new Asso,
        'parents' => [
          'secretaire general',
        ],
				'permissions' => [
          'calendar',
          'event',
          'contact',
          'article',
          'data',
          'reservation',
				],
			],
			[
				'type' => 'treasury',
				'name' => 'Trésorier',
				'description' => 'Responsable de la trésorie',
				'limited_at' => 1,
				'owned_by' => new Asso,
				'parents' => [
					'vice-president',
				],
				'permissions' => [
					'treasury',
          'event',
				]
			],
			[
				'type' => 'vice-treasury',
				'name' => 'Vice-Trésorier',
				'description' => 'Co-responsable de la trésorie',
				'limited_at' => 4,
				'owned_by' => new Asso,
				'parents' => [
          'treasury',
				],
				'permissions' => [
					'treasury',
          'event',
				]
			],
			[
				'type' => 'bureau',
				'name' => 'Bureau',
				'description' => 'Membre du bureau',
				'owned_by' => new Asso,
				'parents' => [
					'vice-president',
          'secretaire general',
					'vice-secretaire',
          'treasury',
					'vice-treasury',
				],
				'permissions' => [
          'event',
				]
			],
			[
				'type' => 'resp informatique',
				'name' => 'Responsable Informatique',
				'description' => 'Responsable informatique de l\'association',
        'limited_at' => 1,
				'owned_by' => new Asso,
				'parents' => [
					'bureau',
				],
				'permissions' => [
          'calendar',
          'event',
          'article'
				],
			],
			[
				'type' => 'developer',
				'name' => 'Développeur',
				'description' => 'Membre de l\'équipe informatique de l\'association',
				'owned_by' => new Asso,
				'parents' => [
					'resp informatique',
				],
			],
			[
				'type' => 'resp communication',
				'name' => 'Responsable Communication',
				'description' => 'Responsable communication de l\'association',
        'limited_at' => 1,
				'owned_by' => new Asso,
				'parents' => [
					'bureau',
				],
				'permissions' => [
          'event',
          'article',
          'comment',
          'data',
				],
			],
			[
				'type' => 'communication',
				'name' => 'Chargé de communication',
				'description' => 'Membre de l\'équipe communication de l\'association',
				'owned_by' => new Asso,
				'parents' => [
					'resp communication',
				],
			],
			[
				'type' => 'resp animation',
				'name' => 'Responsable animation',
				'description' => 'Responsable animation de l\'association',
        'limited_at' => 1,
				'owned_by' => new Asso,
				'parents' => [
					'bureau',
				],
				'permissions' => [
          'event',
				],
			],
			[
				'type' => 'animation',
				'name' => 'Chargé de l\'animation',
				'description' => 'Membre de l\'équipe animation de l\'association',
				'owned_by' => new Asso,
				'parents' => [
					'resp animation',
				],
			],
			[
				'type' => 'resp partenariat',
				'name' => 'Responsable partenariat',
				'description' => 'Responsable partenariat de l\'association',
        'limited_at' => 1,
				'owned_by' => new Asso,
				'parents' => [
					'bureau',
				],
				'permissions' => [
          'event',
				],
			],
			[
				'type' => 'partenariat',
				'name' => 'Chargé du partenariat',
				'description' => 'Membre de l\'équipe partenariat de l\'association',
				'owned_by' => new Asso,
				'parents' => [
					'resp partenariat',
				],
			],
			[
				'type' => 'resp logistique',
				'name' => 'Responsable logistique',
				'description' => 'Responsable logistique de l\'association',
        'limited_at' => 1,
				'owned_by' => new Asso,
				'parents' => [
					'bureau',
				],
				'permissions' => [
          'event',
          'reservation',
          'bobby',
				],
			],
			[
				'type' => 'logistique',
				'name' => 'Chargé de la logistique',
				'description' => 'Membre de l\'équipe logistique de l\'association',
				'owned_by' => new Asso,
				'parents' => [
					'resp logistique',
				],
        'permissions' => [
          'bobby',
        ],
			],
			[
				'type' => 'resp',
				'name' => 'Responsable',
				'description' => 'Responsable dans l\'association',
				'owned_by' => new Asso,
				'parents' => [
					'bureau',
				],
				'permissions' => [
          'event',
				],
			],
			[
				'type' => 'membre',
				'name' => 'Membre de l\'association',
				'description' => 'Membre de l\'équipe associative',
				'owned_by' => new Asso,
				'parents' => [
					'resp',
				],
			],
			[
				'type' => 'group admin',
				'name' => 'Administrateur',
				'description' => 'Administrateur du group',
				'limited_at' => 1,
        'owned_by' => new Group,
				'permissions' => [
          'member',
          'calendar',
          'event',
          'contact',
          'article',
          'role',
				],
			],
			[
				'type' => 'group planner',
				'name' => 'Planificateur',
				'description' => 'Personne planifiant les évènements et les calendriers du groupe',
        'owned_by' => new Group,
				'permissions' => [
          'calendar',
          'event',
				],
			],
			[
				'type' => 'group writer',
				'name' => 'Ecrivain',
				'description' => 'Personne écrivant les articles du groupe',
        'owned_by' => new Group,
				'permissions' => [
          'article',
				],
			],
		];

		foreach ($roles as $role) {
			Role::create([
				'type' => $role['type'],
				'name' => $role['name'],
				'description' => $role['description'],
				'limited_at' => $role['limited_at'] ?? null,
        'owned_by_id' => $role['owned_by']->id,
        'owned_by_type' => get_class($role['owned_by']),
 			])->givePermissionTo($role['permissions'] ?? [])
				->assignParentRole($role['parents'] ?? []);
		}
    }
}
