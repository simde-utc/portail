<?php

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;
use App\Models\Visibility;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = [
            [
                'user_id'       => User::where('firstname', config('app.admin.firstname'))->first()->id,
                'name'          => 'LA13 Forever',
                'icon'          => null,
                'visibility_id' => Visibility::findByType('public')->id,
            ],
            [
                'user_id'       => User::where('firstname', 'Natan')->first()->id,
                'name'          => 'Coloc',
                'icon'          => null,
                'visibility_id' => Visibility::findByType('private')->id,
            ],
            [
                'user_id'       => User::where('firstname', 'Alexandre')->first()->id,
                'name'          => 'Mon groupe sur invitation <3',
                'icon'          => null,
                'visibility_id' => Visibility::findByType('private')->id,
            ],
        ];

        foreach ($groups as $group => $values) {
            Group::create($values);
        }
    }
}
