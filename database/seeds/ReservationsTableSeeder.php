<?php

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Asso;
use App\Models\Reservation;

class ReservationTableSeeder extends Seeder
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
                'location' => 'BDE-UTC (1er étage)',
                'owner' => Asso::where('login', 'bde')->first(),
            ],
        ];

        foreach ($reservations as $reservation) {
            Reservation::create([
                'location_id' => Location::where('name', $reservation['location'])->first()->id,
            ])->changeOwnerTo($reservation['owner'])->save();
        }
    }
}
