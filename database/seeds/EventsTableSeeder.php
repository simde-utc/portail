<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asso;
use App\Models\Location;
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
				'name'     	=> 'Première réunion - Portail',
				'location'	=> 'Salle de réunion 1 (1er étage)',
				'begin_at'	=> '2018-04-03 16:30',
				'end_at'	=> '2018-04-03 18:30',
				'owner'		=> User::find(1)
			],
			[
				'name'     	=> 'Seconde réunion - Portail',
				'location'	=> 'Salle de réunion 1 (1er étage)',
				'begin_at'	=> '2018-04-10 16:30',
				'end_at'	=> '2018-04-10 18:30',
				'owner'		=> User::find(2)
			],
			[
				'name'     	=> 'JDA',
				'location'	=> 'Maison Des Etudiants (MDE)',
				'begin_at'	=> '2018-09-07',
				'end_at'	=> '2018-09-07',
				'full_day'	=> true,
				'owner'		=> Asso::find(1)
			],
		];

		foreach ($events as $event) {
			Event::create([
				'name'			=> $event['name'],
				'location_id'	=> Location::where('name', $event['location'])->first()->id,
				'begin_at'		=> Carbon::parse($event['begin_at']),
				'end_at'		=> Carbon::parse($event['end_at']),
				'full_day'		=> $event['full_day'] ?? false,
			])->changeOwnerTo($event['owner'])->save();
		}
	}
}
