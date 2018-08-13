<?php

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;
use App\Models\Asso;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = [
            [
                'user_id'       => User::where('firstname', 'Samy')->first()->id,
                'name'          => 'Client de Samy',
                'secret'        => 'password',
                'personal_access_client'    => '0',
                'password_client'           => '0',
                'revoked'       => 0,
                'redirect'      => 'http://samy.utc.fr',
                'asso_id'       => Asso::where('login', 'simde')->first()->id,
                'scopes'        => '',
            ],
            [
                'user_id'       => User::where('firstname', 'Natan')->first()->id,
                'name'          => 'Client de Natan',
                'secret'        => 'password',
                'personal_access_client'    => '0',
                'password_client'           => '0',
                'revoked'       => 0,
                'redirect'      => 'http://danous.utc.fr',
                'asso_id'       => Asso::where('login', 'simde')->first()->id,
                'scopes'        => '',
            ],
        ];

        foreach ($clients as $client => $values)
            Client::create($values);
    }
}
