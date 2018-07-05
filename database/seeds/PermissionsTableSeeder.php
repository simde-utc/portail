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
				'type' => 'tresorie',
				'name' => 'Trésorie',
				'description' => 'Gestion de la trésorie de l\'association',
                'only_for' => 'assos',
			],
			[
				'type' => 'billetterie',
				'name' => 'Billetterie',
				'description' => 'Gestion de la billetterie de l\'association',
                'only_for' => 'assos',
			],
			[
				'type' => 'calendrier',
				'name' => 'Calendrier',
				'description' => 'Gestion des calendriers de l\'association',
                'only_for' => 'assos',
			],
			[
				'type' => 'evenement',
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
				'type' => 'groupe membre',
				'name' => 'Membre',
				'description' => 'Gestion des membres du groupe',
                'only_for' => 'groups',
			],
			[
				'type' => 'utilisateur',
				'name' => 'Utilisateur',
				'description' => 'Gestion des utilisateurs',
			],
			[
				'type' => 'asso',
				'name' => 'Association',
				'description' => 'Gestion des associations',
			],
			[
				'type' => 'groupe',
				'name' => 'Groupe',
				'description' => 'Gestion des groupes',
			],
		];

		foreach ($permissions as $permission) {
			Permission::create($permission);
		}
    }
}
