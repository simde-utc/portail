<?php

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Asso;
use App\Models\BookingType;

class BookingsTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            [
                'type' => 'meeting',
                'name' => 'Réunion',
                'need_validation' => false,
            ],
            [
                'type' => 'presentation',
                'name' => 'Présentation',
                'need_validation' => false,
            ],
            [
                'type' => 'conference',
                'name' => 'Conférence',
                'need_validation' => false,
            ],
            [
                'type' => 'logistic',
                'name' => 'Logistique',
            ],
            [
                'type' => 'work',
                'name' => 'Groupe de travail',
            ],
            [
                'type' => 'other',
                'name' => 'Autre',
            ],
        ];

        foreach ($types as $type) {
            BookingType::create($type);
        }
    }
}
