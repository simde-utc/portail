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
                'id'		=> '45617374-6572-2065-6767-7321202b5f2b',
                'email'     => config('app.admin.email'),
                'firstname' => config('app.admin.firstname'),
                'lastname'  => config('app.admin.lastname'),
                'role'		=> 'superadmin',
                'assos'		=> [
                    'simde' => 'president',
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
                'firstname' => 'Alexandre',
                'lastname'  => 'Brasseur',
                'role'		=> 'admin',
                'assos'		=> [
                    'simde' => 'developer',
                ],
            ],
            [
                'email'     => 'romain.maliach-auguste@etu.utc.fr',
                'firstname' => 'Romain',
                'lastname'  => 'Maliach-Auguste',
                'role'		=> 'admin',
                'assos'		=> [
                    'simde'	=> 'developer',
                ]
            ],
            [
                'email'     => 'cesar.richard@hds.utc.fr',
                'firstname' => 'Cesar',
                'lastname'  => 'Richard',
                'role'		=> 'superadmin',
                'assos'		=> [
                    'simde'	=> 'secretaire general',
                ]
            ],
            [
                'email'     => 'josselin.pennors@etu.utc.fr',
                'firstname' => 'Josselin',
                'lastname'  => 'Pennors',
                'role'		=> 'admin',
                'assos'		=> [
                    'simde'	=> 'developer',
                ]
            ],
        ];

        foreach ($users as $user) {
            $model = User::create([
                'id'		=> ($user['id'] ?? null),
                'email'     => $user['email'],
                'firstname' => $user['firstname'],
                'lastname'  => strtoupper($user['lastname']),
            ])->assignRoles($user['role'], [
                'validated_by_id' => User::first()->id,
            ], true);

            foreach (($user['assos'] ?? []) as $name => $role) {
                Asso::where('login', $name)->first()->assignRoles($role, [
                    'user_id' => $model->id,
                    'validated_by_id' => User::first()->id,
                ], true);
            }
        }
    }
}
