<?php

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Asso;
use App\Models\Booking;
use App\Models\BookingType;
use App\Models\Room;
use App\Models\Event;
use App\Models\User;
use App\Models\Visibility;

class BookingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bookings = [
            [
                'name' => 'Réunion de bienvenue',
                'begin_at' => '2018-04-03 16:30',
                'end_at' => '2018-04-03 18:30',
                'location' => 'BDE-UTC (1er étage)',
                'created_by' => User::where('firstname', 'Samy')->first(),
                'owned_by' => Asso::where('login', 'bde')->first(),
                'validated_by' => Asso::where('login', 'bde')->first(),
                'booking_type' => 'meeting',
            ],
        ];

        foreach ($bookings as $booking) {
            $location = Location::where('name', $booking['location'])->first();
            $room = Room::where('location_id', $location->id)->first();
            $calendar = $room->calendar;

            $event = Event::create([
                'name' => ($booking['name'] ?? BookingType::find($booking['booking_type'])),
                'begin_at' => $booking['begin_at'],
                'end_at' => $booking['end_at'],
                'full_day' => ($booking['full_day'] ?? false),
                'location_id' => $location->id,
                'created_by_id' => $booking['created_by']->id,
                'created_by_type' => get_class($booking['created_by']),
                'owned_by_id' => $calendar->owned_by_id,
                'owned_by_type' => $calendar->owned_by_type,
                'visibility_id' => $calendar->visibility_id,
            ]);

            Booking::create([
                'room_id' => $room->id,
                'event_id' => $event->id,
                'type_id' => BookingType::where('type', $booking['booking_type'])->first()->id,
                'created_by_id' => $booking['created_by']->id,
                'created_by_type' => get_class($booking['created_by']),
                'validated_by_id' => $booking['validated_by']->id,
                'validated_by_type' => get_class($booking['validated_by']),
            ])->changeOwnerTo($booking['owned_by'])->save();
        }
    }
}
