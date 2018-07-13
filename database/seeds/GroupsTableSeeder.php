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
				'user_id'       => User::find(1)->id,
				'name'          => 'LA13 Forever',
				'icon'          => null,
				'visibility_id' => Visibility::where('type', 'public')->first()->id,
			],
            [
                'user_id'       => User::find(3)->id,
                'name'          => 'Coloc',
                'icon'          => null,
                'visibility_id' => Visibility::where('type', 'private')->first()->id,
            ],
			[
				'user_id'       => User::find(2)->id,
				'name'          => 'Mon groupe sur invitation <3',
				'icon'          => null,
				'visibility_id' => Visibility::where('type', 'private')->first()->id,
			],
        ];

        foreach ($groups as $group => $values)
            Group::create($values);
    }
}
