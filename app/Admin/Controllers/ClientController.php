<?php

namespace App\Admin\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\Asso;

class ClientController extends ResourceController
{
    protected $model = Client::class;

    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'user' => User::get(['id', 'lastname', 'firstname']),
            'asso' => Asso::get(['id', 'name']),
            'name' => 'text',
            'secret' => 'text',
            'redirect' => 'text',
            'targeted_types' => 'text',
            'personal_access_client' => 'switch',
            'password_client' => 'switch',
            'created_at' => 'display',
            'updated_at' => 'display'
        ];
    }

    protected function getDefaults(): array
    {
        return [
            'user_id' => \Auth::guard('admin')->id()
        ];
    }

    protected function getWith(): array
    {
        return [
            'user', 'asso'
        ];
    }
}
