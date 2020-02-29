<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asso;
use App\Models\Semester;
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

        fprintf(STDOUT, "Seeding manually added users\n");
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

        // Giving information
        fprintf(STDOUT, "Seeding random users\n");
        $this->command->getOutput()->progressStart(config('seeder.user.amount'));

        // Seeding random users
        for ($i = 1; $i <= config('seeder.user.amount') ; $i++) {
            $user = factory(User::class)->create()->save();
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();

        // Fetching common data
        $presidentRoleId = Role::where('type', 'president')->first()->id;
        $roles = Role::where([
            ["owned_by_type", "=", 'App\Models\Asso'],
            ["owned_by_id" , "=", null]
        ])->get()->toArray();
        $assos = Asso::whereNull('in_cemetery_at')->get()->toArray();
        $admin_id = User::where("email", config('app.admin.email'))->first()->id;
        $semester_id = Semester::getThisSemester()->id;
        if (config("seeder.membership.multiple_semesters")) {
            $semesters = Semester::all()->toArray();
        }

        // Start progress bar
        fprintf(STDOUT, "Seeding memberships\n");
        $this->command->getOutput()->progressStart(config('seeder.membership.amount'));

        // Seeding
        for ($i = 1; $i <= config('seeder.membership.amount'); $i++) {
            $faker = Faker\Factory::create();
            $user_id = $faker->randomElement(User::all()->toArray())['id'];
            $asso = Asso::where('id', $faker->randomElement($assos)['id'])->first();

            if (config("seeder.membership.multiple_semesters")) {
                $semester_id = $faker->randomElement($semesters)['id'];
            }

            if ($asso->members()->where("semester_id", $semester_id)->count() == 0) {
                try {
                    $asso->assignRoles("president", [
                        'user_id' => $user_id,
                        'validated_by_id' => $admin_id,
                        'semester_id' => $semester_id,
                    ], true);
                    $this->command->getOutput()->progressAdvance();
                } catch (\Throwable $e) {
                    $i--;
                }

                continue;
            }

            $validatedById = $asso->members()->where('role_id', $presidentRoleId)->first()->id;
            $role = $faker->randomElement($roles)['id'];
            try {
                $asso->assignRoles($role, [
                    'user_id' => $user_id,
                    'validated_by_id' => $validatedById,
                    'semester_id' => $semester_id,
                ]);
                $this->command->getOutput()->progressAdvance();
            } catch (\Throwable $e) {
                    $i--;
            }
        }

        $this->command->getOutput()->progressFinish();
    }
}
