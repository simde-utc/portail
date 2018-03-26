<?php

use Illuminate\Database\Seeder;
use App\Models\Asso;
use App\Models\Room;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rooms = [
            [
                'name'       => 'Salle PVDC',
                'asso_id'          => Asso::find(1)->id,
            ],
            [
                'name'       => 'Salle PAE',
                'asso_id'          => Asso::find(2)->id,
            ],
            [
                'name'       => 'Salle de réunion',
                'asso_id'          => Asso::find(3)->id,
            ],
        ];

        foreach ($rooms as $room => $values){
            Room::create($values);
        }
    }
}
