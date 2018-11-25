<?php

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Asso;
use App\Models\Room;
use App\Models\Visibility;

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
                'location' => 'BDE-UTC (1er étage)',
                'capacity' => 49,
                'visibility' => 'contributorBde',
                'owner' => Asso::where('login', 'bde')->first(),
            ],
        ];

        foreach ($rooms as $room) {
            Room::create([
                'location_id' => Location::where('name', $room['location'])->first()->id,
                'capacity' => $room['capacity'],
                'visibility_id' => Visibility::where('type', $room['visibility'])->first()->id,
            ])->changeOwnerTo($room['owner'])->save();
        }
    }
}
