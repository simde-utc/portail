<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asso;
use App\Models\Location;
use App\Models\Visibility;
use App\Models\Calendar;
use App\Models\Event;
use Carbon\Carbon;

class CalendarsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $calendars = [
            [
                'name'     		=> 'Personnel',
                'description'	=> 'Calendrier personnel',
                'color'			=> '#00FF00',
                'visibility'	=> 'private',
                'created_by'	=> User::where('email', config('app.admin.email'))->first(),
                'owner'			=> User::where('email', config('app.admin.email'))->first(),
                'events'		=> [
                    'Petit pic des familles',
                    'Petite chose perso'
                ],
            ],
            [
                'name'     		=> 'Assos',
                'description'	=> 'Calendrier associatif',
                'color'			=> '#0000FF',
                'visibility'	=> 'private',
                'created_by'	=> User::where('email', config('app.admin.email'))->first(),
                'owner'			=> User::where('email', config('app.admin.email'))->first(),
                'events'		=> [
                    'Première réunion - Portail',
                    'Seconde réunion - Portail',
                ],
            ],
            [
                'name'     		=> 'Assos',
                'description'	=> 'Calendrier associatif',
                'color'			=> '#0000FF',
                'visibility'	=> 'private',
                'created_by'	=> User::where('email', 'natan.danous@etu.utc.fr')->first(),
                'owner'			=> User::where('email', 'natan.danous@etu.utc.fr')->first(),
                'events'		=> [
                    'Première réunion - Portail',
                    'Seconde réunion - Portail',
                ],
            ],
            [
                'name'     		=> 'Assos',
                'description'	=> 'Calendrier associatif',
                'color'			=> '#0000FF',
                'visibility'	=> 'private',
                'created_by'	=> User::where('email', 'alexandre.brasseur@etu.utc.fr')->first(),
                'owner'			=> User::where('email', 'alexandre.brasseur@etu.utc.fr')->first(),
                'events'		=> [
                    'Seconde réunion - Portail',
                ],
            ],
            [
                'name'     		=> 'Assos',
                'description'	=> 'Calendrier associatif',
                'color'			=> '#0000FF',
                'visibility'	=> 'private',
                'created_by'	=> User::where('email', 'romain.maliach-auguste@etu.utc.fr')->first(),
                'owner'			=> User::where('email', 'romain.maliach-auguste@etu.utc.fr')->first(),
                'events'		=> [
                    'Seconde réunion - Portail',
                ],
            ],
            [
                'name'     		=> 'Cours',
                'description'	=> 'Calendrier de tous mes cours',
                'color'			=> '#FFC0CB',
                'visibility'	=> 'cas',
                'created_by'	=> Asso::where('login', 'simde')->first(),
                'owner'			=> User::where('email', config('app.admin.email'))->first(),
                'events'		=> [
                    'LA13'
                ],
            ],
            [
                'name'     		=> 'Cours',
                'description'	=> 'Calendrier de tous mes cours',
                'color'			=> '#FFC0CB',
                'visibility'	=> 'cas',
                'created_by'	=> Asso::where('login', 'simde')->first(),
                'owner'			=> User::where('email', 'alexandre.brasseur@etu.utc.fr')->first(),
                'events'		=> [
                    'MT90/91'
                ],
            ],
            [
                'name'     		=> 'Cours',
                'description'	=> 'Calendrier de tous mes cours',
                'color'			=> '#FFC0CB',
                'visibility'	=> 'cas',
                'created_by'	=> Asso::where('login', 'simde')->first(),
                'owner'			=> User::where('email', 'natan.danous@etu.utc.fr')->first(),
                'events'		=> [
                    'MT90/91'
                ],
            ],
            [
                'name'     		=> 'Evènements',
                'description'	=> 'Calendrier des évènements du BDE-UTC',
                'color'			=> '#0000FF',
                'visibility'	=> 'public',
                'created_by'	=> Asso::where('login', 'bde')->first(),
                'owner'			=> Asso::where('login', 'bde')->first(),
                'events'		=> [
                    'JDA'
                ],
                'followers'		=> [
                    User::where('email', config('app.admin.email'))->first(),
                    User::where('email', 'natan.danous@etu.utc.fr')->first(),
                ],
            ],
            [
                'name'     		=> 'Réunions',
                'description'	=> 'Calendrier des réunions du BDE-UTC',
                'color'			=> '#FF0000',
                'visibility'	=> 'private',
            // Only visible by members.
                'created_by'	=> Asso::where('login', 'bde')->first(),
                'owner'			=> Asso::where('login', 'bde')->first(),
                'followers'		=> [
                    User::where('email', config('app.admin.email'))->first()
                ],
            ],
            [
                'name'     		=> 'Evènements',
                'description'	=> 'Calendrier des évènements du SiMDE',
                'color'			=> '#0000FF',
                'visibility'	=> 'public',
                'created_by'	=> Asso::where('login', 'simde')->first(),
                'owner'			=> Asso::where('login', 'simde')->first(),
                'followers'		=> [
                    User::where('email', config('app.admin.email'))->first(),
                    User::where('email', 'alexandre.brasseur@etu.utc.fr')->first(),
                ],
            ],
            [
                'name'     		=> 'Réunions',
                'description'	=> 'Calendrier des réunions du SiMDE',
                'color'			=> '#FF0000',
                'visibility'	=> 'private',
                // Only visible by members.
                'created_by'	=> Asso::where('login', 'simde')->first(),
                'owner'			=> Asso::where('login', 'simde')->first(),
                'events'		=> [
                    'Première réunion - Portail',
                    'Seconde réunion - Portail',
                ],
                'followers'		=> [
                    User::where('email', config('app.admin.email'))->first(),
                    User::where('email', 'alexandre.brasseur@etu.utc.fr')->first(),
                    User::where('email', 'natan.danous@etu.utc.fr')->first(),
                    User::where('email', 'romain.maliach-auguste@etu.utc.fr')->first(),
                ],
            ],
        ];

        foreach ($calendars as $calendar) {
            $model = Calendar::create([
                'name'				=> $calendar['name'],
                'description'		=> $calendar['description'],
                'color'				=> $calendar['color'],
                'visibility_id'		=> Visibility::findByType($calendar['visibility'])->id,
                'created_by_id'		=> $calendar['created_by']->id,
                'created_by_type'	=> get_class($calendar['created_by']),
            ])->changeOwnerTo($calendar['owner']);

            $model->save();

            foreach (($calendar['events'] ?? []) as $event) {
                $model->events()->attach(Event::where('name', $event)->first());
            }

            foreach (($calendar['followers'] ?? []) as $follower) {
                $model->followers()->attach($follower);
            }
        }
    }
}
