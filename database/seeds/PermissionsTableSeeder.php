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
				'type' => 'treasury',
				'name' => 'Trésorerie',
				'description' => 'Gestion de la trésorerie de l\'association',
                'only_for' => 'assos',
			],
			[
				'type' => 'ticketing',
				'name' => 'Billetterie',
				'description' => 'Gestion de la billetterie de l\'association',
                'only_for' => 'assos',
			],
			[
				'type' => 'calendar',
				'name' => 'Calendrier',
				'description' => 'Gestion des calendriers de l\'association',
                'only_for' => 'assos',
			],
			[
				'type' => 'event',
				'name' => 'Evènement',
				'description' => 'Gestion des évènements de l\'association',
                'only_for' => 'assos',
			],
			[
				'type' => 'article',
				'name' => 'Articles',
				'description' => 'Gestion des articles de l\'association',
                'only_for' => 'assos',
			],
			[
				'type' => 'group member',
				'name' => 'Membre',
				'description' => 'Gestion des membres du groupe',
                'only_for' => 'groups',
			],
			[
				'type' => 'user',
				'name' => 'Utilisateur',
				'description' => 'Gestion des utilisateurs',
			],
			[
				'type' => 'asso',
				'name' => 'Association',
				'description' => 'Gestion des associations',
			],
			[
				'type' => 'group',
				'name' => 'Groupe',
				'description' => 'Gestion des groupes',
			],
		];

		foreach ($permissions as $permission) {
			Permission::create($permission);
		}
    }
}
