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
            VisibilitiesTableSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            ContactsTypesTableSeeder::class,
            AssosTableSeeder::class,
            ServicesTableSeeder::class,
            PlacesAndLocationsTableSeeder::class,
            RoomsTableSeeder::class,
            ReservationsTypesTableSeeder::class,
            AccessTableSeeder::class,
            AdminMenuTableSeeder::class,
        ]);

        if (config('app.debug', false)) {
            $this->call([
                UsersTableSeeder::class,
                GroupsTableSeeder::class,
                ArticlesTableSeeder::class,
                PartnersTableSeeder::class,
                EventsTableSeeder::class,
                CalendarsTableSeeder::class,
                ClientsTableSeeder::class,
                CommentsTableSeeder::class,
                ReservationsTableSeeder::class,
            ]);
        }
    }
}
