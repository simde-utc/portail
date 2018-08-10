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
                'user_id'       => User::find(1)->id,
                'name'          => 'Client de Samy',
                'secret'        => 'password',
                'personal_access_client'    => '0',
                'password_client'           => '0',
                'revoked'       => 0,
                'redirect'      => 'http://samy.utc.fr',
                'asso_id'       => Asso::find(1)->id,
                'scopes'        => '',
            ],
            [
                'user_id'       => User::find(3)->id,
                'name'          => 'Client de Natan',
                'secret'        => 'password',
                'personal_access_client'    => '0',
                'password_client'           => '0',
                'revoked'       => 0,
                'redirect'      => 'http://danous.utc.fr',
                'asso_id'       => Asso::find(2)->id,
                'scopes'        => '',
            ],
        ];

        foreach ($clients as $client => $values)
            Client::create($values);
    }
}
