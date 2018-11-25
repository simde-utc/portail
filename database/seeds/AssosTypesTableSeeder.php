<?php

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
        $types = [
            '1901' => 'Association loi 1901',
            'commission' => 'Commission',
            'club' => 'Club',
            'projet' => 'Projet',
        ];

        foreach ($types as $type => $name) {
            AssoType::create([
                'type' => $type,
                'name' => $name,
            ]);
        }
    }
}
