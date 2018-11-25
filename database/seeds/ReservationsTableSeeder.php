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
                'begin_at' => '2018-04-03 16:30',
                'end_at' => '2018-04-03 18:30',
                'location' => 'BDE-UTC (1er étage)',
                'created_by' => User::where('firstname', 'Samy')->first(),
                'owned_by' => Asso::where('login', 'bde')->first(),
                'validated_by' => Asso::where('login', 'bde')->first(),
                'reservation_type' => 'meeting',
            ],
        ];

        foreach ($reservations as $reservation) {
            $location = Location::where('name', $reservation['location'])->first();
            $room = Room::where('location_id', $location->id)->first();
            $calendar = $room->calendar;

            $event = Event::create([
                'name' => ($reservation['name'] ?? ReservationType::find($reservation['reservation_type'])),
                'begin_at' => $reservation['begin_at'],
                'end_at' => $reservation['end_at'],
                'full_day' => ($reservation['full_day'] ?? false),
                'location_id' => $location->id,
                'created_by_id' => $reservation['created_by']->id,
                'created_by_type' => get_class($reservation['created_by']),
                'owned_by_id' => $calendar->owned_by_id,
                'owned_by_type' => $calendar->owned_by_type,
                'visibility_id' => $calendar->visibility_id,
            ]);

            Reservation::create([
                'room_id' => $room->id,
                'event_id' => $event->id,
                'reservation_type_id' => ReservationType::where('type', $reservation['reservation_type'])->first()->id,
                'created_by_id' => $reservation['created_by']->id,
                'created_by_type' => get_class($reservation['created_by']),
                'validated_by_id' => $reservation['validated_by']->id,
                'validated_by_type' => get_class($reservation['validated_by']),
            ])->changeOwnerTo($reservation['owned_by'])->save();
        }
    }
}
