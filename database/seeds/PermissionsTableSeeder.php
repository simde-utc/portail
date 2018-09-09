<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;
use App\Models\Asso;
use App\Models\Group;

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
				'type' => 'treasury',
				'name' => 'Trésorerie',
				'description' => 'Gestion de la trésorerie de l\'association',
        'owned_by' => new Asso,
			],
			[
				'type' => 'ticketing',
				'name' => 'Billetterie',
				'description' => 'Gestion de la billetterie de l\'association',
        'owned_by' => new Asso,
			],
			[
				'type' => 'calendar',
				'name' => 'Calendrier',
				'description' => 'Gestion des calendriers de l\'association',
        'owned_by' => new Asso,
			],
			[
				'type' => 'event',
				'name' => 'Evènement',
				'description' => 'Gestion des évènements de l\'association',
        'owned_by' => new Asso,
			],
			[
				'type' => 'data',
				'name' => 'Informations',
				'description' => 'Gestion des informations concernant l\'association',
        'owned_by' => new Asso,
			],
			[
				'type' => 'contact',
				'name' => 'Contact',
				'description' => 'Gestion des moyens de contact de l\'association',
        'owned_by' => new Asso,
			],
			[
				'type' => 'article',
				'name' => 'Article',
				'description' => 'Gestion des articles de l\'association',
        'owned_by' => new Asso,
			],
			[
				'type' => 'reservation',
				'name' => 'Réservation',
				'description' => 'Gestion des réservations',
        'owned_by' => new Asso,
			],
			[
				'type' => 'role',
				'name' => 'Rôle',
				'description' => 'Gestion des rôles',
        'owned_by' => new Asso,
			],
			[
				'type' => 'member',
				'name' => 'Membre',
				'description' => 'Gestion des membres du groupe',
        'owned_by' => new Group,
			],
			[
				'type' => 'calendar',
				'name' => 'Calendrier',
				'description' => 'Gestion des calendriers du groupe',
        'owned_by' => new Group,
			],
			[
				'type' => 'event',
				'name' => 'Evènement',
				'description' => 'Gestion des évènements du groupe',
        'owned_by' => new Group,
			],
			[
				'type' => 'contact',
				'name' => 'Contact',
				'description' => 'Gestion des contacts du groupe',
        'owned_by' => new Group,
			],
			[
				'type' => 'role',
				'name' => 'Rôle',
				'description' => 'Gestion des rôles',
        'owned_by' => new Group,
			],
			[
				'type' => 'user',
				'name' => 'Utilisateur',
				'description' => 'Gestion des utilisateurs',
        'owned_by' => new User,
			],
			[
				'type' => 'asso',
				'name' => 'Association',
				'description' => 'Gestion des associations',
        'owned_by' => new User,
			],
			[
				'type' => 'service',
				'name' => 'Service',
				'description' => 'Gestion des services',
        'owned_by' => new User,
			],
			[
				'type' => 'group',
				'name' => 'Groupe',
				'description' => 'Gestion des groupes',
        'owned_by' => new User,
			],
			[
				'type' => 'client',
				'name' => 'Client',
				'description' => 'Gestion des clients',
        'owned_by' => new User,
			],
			[
				'type' => 'room',
				'name' => 'Salle de réservation',
				'description' => 'Gestion des salles de réservations',
        'owned_by' => new User,
			],
			[
				'type' => 'role',
				'name' => 'Rôle',
				'description' => 'Gestion des rôles',
        'owned_by' => new User,
			],
		];

		foreach ($permissions as $permission)
			$model = Permission::create([
				'type' => $permission['type'],
				'name' => $permission['name'],
        'description' => $permission['description'],
        'owned_by_id' => $permission['owned_by']->id,
        'owned_by_type' => get_class($permission['owned_by']),
			]);
  }
}
