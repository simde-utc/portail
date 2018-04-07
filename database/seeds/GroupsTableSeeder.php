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
                'user_id'       => User::find(2)->id,
                'name'          => 'LA13 Forever',
                'icon'          => '',
                'visibility_id' => Visibility::where('type', 'public')->first()->id,
                'is_active'     => 1,
            ],
            [
                'user_id'       => User::find(3)->id,
                'name'          => 'Coloc',
                'icon'          => '',
                'visibility_id' => Visibility::where('type', 'private')->first()->id,
                'is_active'     => 1,
            ],
        ];

        foreach ($groups as $group => $values){
            Group::create($values);
        }
    }
}
