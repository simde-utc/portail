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
                'owner' => Asso::where('login', 'bde')->first(),
                'confirmed_by' => Asso::where('login', 'bde')->first(),
            ],
        ];

        foreach ($reservations as $reservation) {
            $location = Location::where('name', $reservation['location'])->first();
            $room = Room::where('location_id', $location->id)->first();
            $event = Event::create([
                'name' => $reservation['name'],
                'location_id' => $location->id,
                'begin_at' => $reservation['begin_at'],
                'end_at' => $reservation['end_at'],
                'full_day' => $reservation['full_day'] ?? false,
                'visibility_id' => Visibility::where('type', 'contributorBde')->first()->id,
                'created_by_id' => $reservation['created_by']->id,
                'created_by_type' => get_class($reservation['created_by']),
            ])->changeOwnerTo($reservation['owner']);

            $event->save();

            Reservation::create([
                'room_id' => $room->id,
                'reservation_type_id' => ReservationType::where('type', 'meeting')->first()->id,
                'event_id' => $event->id,
                'created_by_id' => $reservation['created_by']->id,
                'created_by_type' => get_class($reservation['created_by']),
                'confirmed_by_id' => $reservation['confirmed_by']->id,
                'confirmed_by_type' => get_class($reservation['confirmed_by']),
            ])->changeOwnerTo($reservation['owner'])->save();
        }
    }
}
