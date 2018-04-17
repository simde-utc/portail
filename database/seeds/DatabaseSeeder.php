<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// Prod:
		$this->call([
			SemestersTableSeeder::class,
			AssosTypesTableSeeder::class,
			AssosTableSeeder::class,
			VisibilitiesTableSeeder::class,
			ContactsTypesTableSeeder::class,
			PermissionsTableSeeder::class,
			RolesTableSeeder::class,
		]);

		// Dev:
		$this->call([
			UsersTableSeeder::class,
			GroupsTableSeeder::class,
			RoomsTableSeeder::class,
			ArticlesTableSeeder::class,
			PartnersTableSeeder::class,
		]);
	}
}
