<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asso;
use App\Models\Location;
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
				'owner'		=> User::find(1),
			],
			[
				'name'     	=> 'LA13',
				'location'	=> 'Bâtiment B',
				'begin_at'	=> '2018-04-01 16:30',
				'end_at'	=> '2018-04-01 21:30',
				'owner'		=> Asso::find(6), // Théoriquement ici, ça devrait être le client emploidutemps
				'details'	=> [
					'semester' => 'P18',
				],
			],
			[
				'name'     	=> 'MT90/91',
				'location'	=> 'Bâtiment A',
				'begin_at'	=> '2018-04-01 16:30',
				'end_at'	=> '2018-04-01 21:30',
				'owner'		=> Asso::find(6), // Théoriquement ici, ça devrait être le client emploidutemps
				'details'	=> [
					'semester' => 'P18',
				],
			],
			[
				'name'     	=> 'Première réunion - Portail',
				'location'	=> 'Salle de réunion 1 (1er étage)',
				'begin_at'	=> '2018-04-03 16:30',
				'end_at'	=> '2018-04-03 18:30',
				'owner'		=> Asso::find(6),
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
				'begin_at'	=> '2018-04-10 16:30',
				'end_at'	=> '2018-04-10 18:30',
				'owner'		=> Asso::find(6),
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
				'owner'		=> Asso::find(1),
				'details'	=> [
					'categories' 	=> [
						'ASSOS', 'RENCONTRES'
					],
				],
			],
		];

		foreach ($events as $event) {
			$model = Event::create([
				'name'			=> $event['name'],
				'location_id'	=> Location::where('name', $event['location'])->first()->id,
				'begin_at'		=> Carbon::parse($event['begin_at']),
				'end_at'		=> Carbon::parse($event['end_at']),
				'full_day'		=> $event['full_day'] ?? false,
			])->changeOwnerTo($event['owner']);

			$model->save();

			foreach ($event['details'] ?? [] as $key => $value)
				EventDetail::create([
					'event_id' => $model->id,
					'key' => $key,
					'value' => $value,
				]);
		}
	}
}
