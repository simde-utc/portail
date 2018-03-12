<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class VisibilityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $visibilities = ['public', 'logged', 'cas', 'contributor']; // Visibilité contributor = cotisant
        foreach ($visibilities as $visibility) {
          DB::table('visibilities')->insert([
            'name' => $visibility,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
          ]);
        }
    }
}
