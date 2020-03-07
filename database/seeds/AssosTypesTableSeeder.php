<?php
/**
 * Assos Types Seeder
 *
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

use Illuminate\Database\Seeder;
use App\Models\AssoType;

class AssosTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $typeIdentifiers = explode(',', config('seeder.asso.type.identifiers'));
        $typeDescriptions = explode(',', config('seeder.asso.type.descriptions'));

        foreach ($typeIdentifiers as $index => $typeIdentifier) {
            AssoType::create([
                'type' => $typeIdentifier,
                'name' => $typeDescriptions[$index],
            ]);
        }
    }
}
