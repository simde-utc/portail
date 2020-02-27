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
        // Production seeders, be sure before editing this array.
        $this->call([
            SemestersTableSeeder::class,
            AssosTypesTableSeeder::class,
            VisibilitiesTableSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            ContactsTypesTableSeeder::class,
            PlacesAndLocationsTableSeeder::class,
            BookingsTypesTableSeeder::class,
            AccessTableSeeder::class,
            AdminMenuTableSeeder::class,
        ]);

        // Developement seeders
        if ((bool) env('APP_DEBUG', config('app.debug', false))) {
            $this->call([
                AssosTableSeeder::class,
                RoomsTableSeeder::class,
                ServicesTableSeeder::class,
                UsersTableSeeder::class,
                GroupsTableSeeder::class,
                ArticlesTableSeeder::class,
                PartnersTableSeeder::class,
                EventsTableSeeder::class,
                CalendarsTableSeeder::class,
                ClientsTableSeeder::class,
                CommentsTableSeeder::class,
                BookingsTableSeeder::class,
            ]);
        }
    }
}
