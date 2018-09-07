<?php

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Asso;
use App\Models\Reservation;
use App\Models\ReservationType;
use App\Models\Room;
use App\Models\Event;
use App\Models\User;
use App\Models\Visibility;

class ReservationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reservations = [
            [
                'name' => 'Réunion de bienvenue',
        				'begin_at'	=> '2018-04-03 16:30',
        				'end_at'	=> '2018-04-03 18:30',
        				'visibility'=> 'private',
                'location' => 'BDE-UTC (1er étage)',
				        'created_by' => User::where('firstname', 'Samy')->first(),
                'owned_by' => Asso::where('login', 'bde')->first(),
                'validated_by' => Asso::where('login', 'bde')->first(),
            ],
        ];

        foreach ($reservations as $reservation) {
            $location = Location::where('name', $reservation['location'])->first();
            $room = Room::where('location_id', $location->id)->first();

            Reservation::create([
                'event' => [
                  'name' => $reservation['name'],
                  'begin_at' => $reservation['begin_at'],
                  'end_at' => $reservation['end_at'],
                  'visibility_id' => Visibility::where('type', $reservation['visibility'])->first()->id,
                  'created_by_id' => $reservation['created_by']->id,
                  'created_by_type' => get_class($reservation['created_by']),
                  'owned_by_id' => $reservation['owned_by']->id,
                  'owned_by_type' => get_class($reservation['owned_by']),
                ],
                'room_id' => $room->id,
                'reservation_type_id' => ReservationType::where('type', 'meeting')->first()->id,
                'created_by_id' => $reservation['created_by']->id,
                'created_by_type' => get_class($reservation['created_by']),
                'validated_by_id' => $reservation['validated_by']->id,
                'validated_by_type' => get_class($reservation['validated_by']),
            ])->changeOwnerTo($reservation['owned_by'])->save();
        }
    }
}
