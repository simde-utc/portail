<?php

use Illuminate\Database\Seeder;
use App\Models\Place;
use App\Models\Location;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class PlacesAndLocationsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$locations = [
			'FA'	=> new Point(49.41567668, 2.81873379),
			'FB'	=> new Point(49.41555279, 2.81839851),
			'FC'	=> new Point(49.41546825, 2.81807307),
			'FD'	=> new Point(49.41518906, 2.81801407),
			'FE'	=> new Point(49.41533451, 2.81941481),
			'FF'	=> new Point(49.4152242, 2.8191144),
			'FG'	=> new Point(49.41512511, 2.81885021),
		];

		$places = [
			[
				'name'     	=> 'Benjamin Franklin - Université de Technologie de Compiègne (BF/UTC)',
				'address' 	=> 'Rue Roger Coutolenc',
				'city'		=> 'Compiègne',
				'country'	=> 'France',
				'position'	=> $locations['FF'],
				'locations'	=> [
					[
						'name'		=> 'Accueil',
					],
					[
						'name'		=> 'Maison Des Etudiants (MDE)',
						'position'	=> $locations['FE'],
					],
					[
						'name'		=> 'Picasso (RDC)',
						'position'	=> $locations['FE'],
					],
					[
						'name'		=> 'BDE-UTC (1er étage)',
						'position'	=> $locations['FE'],
					],
					[
						'name'		=> 'Bureau des élus UTC (1er étage)',
						'position'	=> $locations['FE'],
					],
					[
						'name'		=> 'Polar (1er étage)',
						'position'	=> $locations['FE'],
					],
					[
						'name'		=> 'Salle Shred (1er étage)',
						'position'	=> $locations['FE'],
					],
					[
						'name'		=> 'Salle de réunion 1 (1er étage)',
						'position'	=> $locations['FE'],
					],
					[
						'name'		=> 'Salle de réunion 2 (2ème étage)',
						'position'	=> $locations['FE'],
					],
					[
						'name'		=> 'Bâtiment A',
						'position'	=> $locations['FA'],
					],
					[
						'name'		=> 'Bâtiment B',
						'position'	=> $locations['FB'],
					],
					[
						'name'		=> 'Bâtiment C',
						'position'	=> $locations['FC'],
					],
					[
						'name'		=> 'Bâtiment D',
						'position'	=> $locations['FD'],
					],
					[
						'name'		=> 'Bâtiment E',
						'position'	=> $locations['FE'],
					],
					[
						'name'		=> 'Bâtiment F',
						'position'	=> $locations['FF'],
					],
					[
						'name'		=> 'Le Philantrope',
						'position'	=> new Point(49.41552063, 2.8190431),
					],
					[
						'name'		=> 'Passerelle',
						'position'	=> new Point(49.41538627, 2.81905919),
					],
					[
						'name'		=> 'Parvis',
						'position'	=> new Point(49.41580681, 2.81900823),
					],
					[
						'name'		=> 'Parking',
						'position'	=> new Point(49.4156009, 2.81851202),
					],
					[
						'name'		=> 'Restaurant Universitaire (CROUS)',
						'position'	=> new Point(49.41529057, 2.82005965),
					],
				],
			],
		];

		foreach ($places as $place) {
			$model = Place::create([
				'name'		=> $place['name'],
				'address'	=> $place['address'],
				'city'		=> $place['city'],
				'country'	=> $place['country'],
				'position'	=> $place['position'],
			]);

			foreach ($place['locations'] as $location) {
				Location::create([
					'name' 		=> $location['name'],
					'place_id' => $model->id,
					'position' => $location['position'] ?? null,
				]);
			}
		}
	}
}
