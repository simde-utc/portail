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
				'created_by'	=> User::find(1),
				'owner'			=> User::find(1),
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
				'owner'			=> User::find(1),
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
				'owner'			=> User::find(2),
				'events'		=> [
					'Seconde réunion - Portail',
				],
			],
			[
				'name'     		=> 'Assos',
				'description'	=> 'Calendrier associatif',
				'color'			=> '#0000FF',
				'visibility'	=> 'private',
				'owner'			=> User::find(3),
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
				'owner'			=> User::find(4),
				'events'		=> [
					'Seconde réunion - Portail',
				],
			],
			[
				'name'     		=> 'Cours',
				'description'	=> 'Calendrier de tous mes cours',
				'color'			=> '#FFC0CB',
				'visibility'	=> 'cas',
				'owner'			=> User::find(1),
				'events'		=> [
					'LA13'
				],
			],
			[
				'name'     		=> 'Cours',
				'description'	=> 'Calendrier de tous mes cours',
				'color'			=> '#FFC0CB',
				'visibility'	=> 'cas',
				'owner'			=> User::find(2),
				'events'		=> [
					'MT90/91'
				],
			],
			[
				'name'     		=> 'Cours',
				'description'	=> 'Calendrier de tous mes cours',
				'color'			=> '#FFC0CB',
				'visibility'	=> 'cas',
				'owner'			=> User::find(3),
				'events'		=> [
					'MT90/91'
				],
			],
			[
				'name'     		=> 'Evènements',
				'description'	=> 'Calendrier des évènements du BDE-UTC',
				'color'			=> '#0000FF',
				'visibility'	=> 'public',
				'owner'			=> Asso::find(1),
				'events'		=> [
					'JDA'
				],
				'followers'		=> [
					User::find(1),
					User::find(3),
				],
			],
			[
				'name'     		=> 'Réunions',
				'description'	=> 'Calendrier des réunions du BDE-UTC',
				'color'			=> '#FF0000',
				'visibility'	=> 'private', // Visible que par les membres
				'owner'			=> Asso::find(1),
				'followers'		=> [
					User::find(1)
				],
			],
			[
				'name'     		=> 'Evènements',
				'description'	=> 'Calendrier des évènements du SiMDE',
				'color'			=> '#0000FF',
				'visibility'	=> 'public',
				'owner'			=> Asso::find(6),
				'followers'		=> [
					User::find(1),
					User::find(2),
				],
			],
			[
				'name'     		=> 'Réunions',
				'description'	=> 'Calendrier des réunions du SiMDE',
				'color'			=> '#FF0000',
				'visibility'	=> 'private', // Visible que par les membres
				'owner'			=> Asso::find(6),
				'events'		=> [
					'Première réunion - Portail',
					'Seconde réunion - Portail',
				],
				'followers'		=> [
					User::find(1),
					User::find(2),
					User::find(3),
					User::find(4),
				],
			],
		];

		foreach ($calendars as $calendar) {
			$model = Calendar::create([
				'name'				=> $calendar['name'],
				'description'		=> $calendar['description'],
				'color'				=> $calendar['color'],
				'visibility_id'		=> Visibility::findByType($calendar['visibility'])->id,
				'created_by_id'		=> isset($calendar['created_by']) ? $calendar['created_by']->id : null,
				'created_by_type'	=> isset($calendar['created_by']) ? get_class($calendar['created_by']) : null,
			])->changeOwnerTo($calendar['owner']);

			$model->save();

			foreach ($calendar['events'] ?? [] as $event)
				$model->events()->attach(Event::where('name', $event)->first());

			foreach ($calendar['followers'] ?? [] as $follower)
				$model->followers()->attach($follower);
		}
	}
}
