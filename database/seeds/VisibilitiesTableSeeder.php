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
        // Visibilités possibles, du plus permissif au moins permissif
        $visibilities = ['public', 'logged', 'cas', 'contributor', 'private', 'owner']; // Visibilité contributor = cotisant

        foreach ($visibilities as $key => $visibility) {
            Visibility::create([
              'name' => $visibility,
              'parent_id' => ($key === 0 ? null : Visibility::where('name', $visibilities[$key - 1])->first()->id),
            ]);
        }
    }
}
