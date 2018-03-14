<?php

use Illuminate\Database\Seeder;
use App\Models\User;

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
                'email' => 'remy.huet@etu.utc.fr',
                'firstname' => 'Rémy',
                'lastname' => 'Huet'
            ],
            [
                'email' => 'samy.nastuzzi@etu.utc.fr',
                'firstname' => 'Samy',
                'lastname' => 'Nastuzzi'
            ]
        ];

        foreach ($users as $user => $values){
            User::create($values);
        }
    }
}
