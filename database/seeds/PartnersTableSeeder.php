<?php
/**
 * Partner seeder
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2020, SiMDE-UTC
 * @license GNU GPL-3.0
 */

use Illuminate\Database\Seeder;
use App\Models\Partner;

class PartnersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Not using the classical way as decribed in laravel factories in order to display log (otherwise it seems too long)
        for ($i = 1; $i <= config('seeder.partner.amount') ; $i++) {
            $partner = factory(Partner::class)->create();
            $partner->save();
            fprintf(STDOUT, "Partner ".$i." \tof ".config('seeder.partner.amount')." created\n");
        }
    }
}
