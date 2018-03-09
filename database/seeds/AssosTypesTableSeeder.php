<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssosTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

	    $types = [
		    '1901' => 'association loi 1901',
		    'commission' => 'commission',
		    'club' => 'club',
		    'projet' => 'projet'
	    ];

        foreach ($types as $type => $description) {
		DB::table('assos_types')->insert([
			'name' => $type,
			'description' => $description,
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now()
		]);
	}
    }
}
