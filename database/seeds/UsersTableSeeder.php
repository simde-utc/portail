<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

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
			],
            [
                'email'     => 'remy.huet@etu.utc.fr',
                'firstname' => 'Rémy',
                'lastname'  => 'Huet',
				'role'		=> 'admin',
            ],
            [
                'email'     => 'natan.danous@etu.utc.fr',
                'firstname' => 'Natan',
                'lastname'  => 'Danous',
				'role'		=> 'admin',
            ]
        ];

        foreach ($users as $user){
            User::create([
				'email'     => $user['email'],
				'firstname' => $user['firstname'],
				'lastname'  => $user['lastname'],
			])->assignRole($user['role']);
        }
    }
}
