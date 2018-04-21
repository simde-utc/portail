<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asso;

class UsersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$users = [
			[
				'email'     => 'samy.nastuzzi@etu.utc.fr',
				'firstname' => 'Samy',
				'lastname'  => 'Nastuzzi',
				'role'		=> 'superadmin',
				'assos'		=> [
					'simde' => 'president',
				],
			],
			[
				'email'     => 'remy.huet@etu.utc.fr',
				'firstname' => 'Rémy',
				'lastname'  => 'Huet',
				'role'		=> 'admin',
				'assos'		=> [
					'simde' => 'developer',
				],
			],
			[
				'email'     => 'natan.danous@etu.utc.fr',
				'firstname' => 'Natan',
				'lastname'  => 'Danous',
				'role'		=> 'admin',
				'assos'		=> [
					'simde' => 'developer',
				],
			],
			[
				'email'     => 'alexandre.brasseur@etu.utc.fr',
				'firstname' => 'Alex',
				'lastname'  => 'Brass',
				'role'		=> 'admin',
				'assos'		=> [
					'simde' => 'developer',
				],
			]
		];

		foreach ($users as $user) {
			$model = User::create([
				'email'     => $user['email'],
				'firstname' => $user['firstname'],
				'lastname'  => $user['lastname'],
			])->assignRoles($user['role'], [
				'validated_by' => 1
			], true);

			foreach ($user['assos'] as $name => $role) {
				Asso::where('login', $name)->first()->assignRoles($role, [
					'user_id' => $model->id,
					'validated_by' => 1,
				], true);
			}
		}
	}
}
