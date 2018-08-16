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
                'id'            => '53616d79-206a-6520-7427-61696d652021',
                'user_id'       => User::where('firstname', 'Samy')->first()->id,
                'name'          => 'Portail dev',
                'secret'        => 'password',
                'personal_access_client'    => '0',
                'password_client'           => '0',
                'revoked'       => 0,
                'redirect'      => 'http://localhost/',
                'asso_id'       => Asso::where('login', 'simde')->first()->id,
                'scopes'        => '',
            ],
            [
                'id'            => '44696575-2065-7374-2075-6e6971756521',
                'user_id'       => User::where('firstname', 'Samy')->first()->id,
                'name'          => 'Appli dev',
                'secret'        => 'password',
                'personal_access_client'    => '0',
                'password_client'           => '1',
                'revoked'       => 0,
                'redirect'      => 'http://localhost/',
                'asso_id'       => Asso::where('login', 'simde')->first()->id,
                'scopes'        => json_encode([
                    'client-create-users-inactive', 'client-create-info-identity-auth-app',
                ]),
            ],
            [
                'id'            => '4a6f7373-656c-696e-203d-20426f626279',
                'user_id'       => User::where('firstname', 'Samy')->first()->id,
                'name'          => 'Bobby dev',
                'secret'        => 'password',
                'personal_access_client'    => '0',
                'password_client'           => '0',
                'revoked'       => 0,
                'redirect'      => 'http://localhost:8000/#!/login',
                'asso_id'       => Asso::where('login', 'simde')->first()->id,
                'scopes'        => '',
            ],
        ];

        foreach ($clients as $client => $values)
            Client::create($values);
    }
}
