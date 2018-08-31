<?php

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
  	    $services = [
            [
                'login' => 'wiki',
                'shortname' => 'Wiki',
                'name' => 'Wiki des Associations',
                'description' => 'Une question associative ? Tout est ici !',
                'url' => url('/wiki'),
            ], [
                'login' => 'payutc',
                'shortname' => 'PayUTC',
                'name' => 'Service de paiement Pay\'UT',
                'description' => 'Besoin de recharger ta carte étudiante ?',
                'url' => 'https://pay.utc.fr',
            ], [
                'login' => 'bdecotiz',
                'shortname' => 'BDE Cotiz',
                'name' => 'Cotisation BDE',
                'description' => 'Pour cotiser rapidement au BDE UTC, c\'est par ici ;) Paiement en Pay\'Ut ou Cb',
                'url' => url('/bde/bdecotiz'),
            ], [
                'login' => 'uvweb',
                'shortname' => 'UVWeb',
                'name' => 'UVWeb',
                'description' => 'Le Hit-Parade des UV Utécéennes',
                'url' => url('/uvweb'),
            ], [
                'login' => 'emploidutemps',
                'shortname' => 'EmploidUT',
                'name' => 'Emploi d\'UTemps',
                'description' => 'Consulter son emploi du temps, l\'exporter, le modifier, le planifier, rien de plus simple !',
                'url' => url('/emploidutemps'),
            ],
        ];

        foreach ($services as $service) {
			Service::create($service);
      	}
    }
}
