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
                'asso_id'          => Asso::where('login', 'pvdc')->first()->id,
            ],
            [
                'name'       => 'Salle PAE',
                'asso_id'          => Asso::where('login', 'pae')->first()->id,
            ],
            [
                'name'       => 'Salle de réunion',
                'asso_id'          => Asso::where('login', 'bde')->first()->id,
            ],
        ];

        foreach ($rooms as $room => $values){
            Room::create($values);
        }
    }
}
