<?php

use Illuminate\Database\Seeder;
use App\Models\Visibility;

class VisibilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $visibilities = ['public', 'logged', 'cas', 'contributor', 'private']; // Visibilité contributor = cotisant

        foreach ($visibilities as $visibility) {
            Visibility::create([
              'name' => $visibility,
            ]);
        }
    }
}
