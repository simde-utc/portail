<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asso;
use App\Models\Location;
use App\Models\Visibility;
use App\Models\EventDetail;
use App\Models\Event;
use Carbon\Carbon;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $events = [
            [
                'name'     	=> 'Petit pic des familles',
                'location'	=> 'Picasso (RDC)',
                'begin_at'	=> '2018-04-01 16:30',
                'end_at'	=> '2018-04-01 21:30',
                'visibility' => 'private',
                'created_by' => User::where('firstname', 'Samy')->first(),
                'owner'		=> User::where('firstname', 'Samy')->first(),
            ],
            [
                'name'     	=> 'Petite chose perso',
                'location'	=> 'Picasso (RDC)',
                'begin_at'	=> '2018-03-05 16:30',
                'end_at'	=> '2018-03-05 21:30',
                'visibility' => 'private',
                'created_by' => User::where('firstname', 'Samy')->first(),
                'owner'		=> User::where('firstname', 'Samy')->first(),
            ],
            [
                'name'     	=> 'TD LA13',
                'location'	=> 'Bâtiment B',
                'begin_at'	=> '2018-04-01 10:15',
                'end_at'	=> '2018-04-01 12:15',
                'visibility' => 'cas',
                'owner'		=> Asso::where('login', 'simde')->first(),
                // Théoriquement ici, ça devrait être le client emploidutemps
                'details'	=> [
                    'semester' => 'P18',
                ],
            ],
            [
                'name'     	=> 'Amphi MT90/91',
                'location'	=> 'Bâtiment A',
                'begin_at'	=> '2018-04-01 08:00',
                'end_at'	=> '2018-04-01 10:00',
                'visibility' => 'cas',
                'owner'		=> Asso::where('login', 'simde')->first(),
                // Théoriquement ici, ça devrait être le client emploidutemps
                'details'	=> [
                    'semester' => 'P18',
                ],
            ],
            [
                'name'     	=> 'Première réunion - Portail',
                'location'	=> 'Salle de réunion 1 (1er étage)',
                'begin_at'	=> '2018-04-03 16:30',
                'end_at'	=> '2018-04-03 18:30',
                'visibility' => 'private',
                'created_by' => User::where('firstname', 'Samy')->first(),
                'owner'		=> Asso::where('login', 'simde')->first(),
                'details'	=> [
                    'description' 	=> 'Réunion de présentation et de recrutement',
                    'invited' 		=> [
                        2, 3
                    ],
                ],
            ],
            [
                'name'     	=> 'Seconde réunion - Portail',
                'location'	=> 'Salle de réunion 1 (1er étage)',
                'begin_at'	=> date('Y-m-d 16:30'),
                'end_at'	=> date('Y-m-d 18:30'),
                'visibility' => 'private',
                'created_by' => User::where('firstname', 'Rémy')->first(),
                'owner'		=> Asso::where('login', 'simde')->first(),
                'details'	=> [
                    'description' 	=> 'Réunion de préparation',
                    'invited' 		=> [
                        1, 3, 4
                    ],
                    'status'		=> 'CANCELED',
                ],
            ],
            [
                'name'     	=> 'JDA',
                'location'	=> 'Maison Des Etudiants (MDE)',
                'begin_at'	=> '2018-09-07',
                'end_at'	=> '2018-09-07',
                'full_day'	=> true,
                'created_by' => User::where('firstname', 'Rémy')->first(),
                'owner'		=> Asso::where('login', 'bde')->first(),
                'visibility' => 'public',
                'details'	=> [
                    'categories' 	=> [
                        'ASSOS', 'RENCONTRES'
                    ],
                ],
            ],
        ];

        foreach ($events as $event) {
            $model = Event::create([
                'name'				=> $event['name'],
                'location_id'		=> Location::where('name', $event['location'])->first()->id,
                'begin_at'			=> Carbon::parse($event['begin_at']),
                'end_at'			=> Carbon::parse($event['end_at']),
                'visibility_id'		=> Visibility::findByType($event['visibility'])->id,
                'full_day'			=> ($event['full_day'] ?? false),
                'created_by_id'		=> isset($event['created_by']) ? $event['created_by']->id : null,
                'created_by_type'	=> isset($event['created_by']) ? get_class($event['created_by']) : null,
            ])->changeOwnerTo($event['owner']);

            $model->save();

            foreach (($event['details'] ?? []) as $key => $value) {
                EventDetail::create([
                    'event_id' => $model->id,
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }
    }
}
