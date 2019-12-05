<?php

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
        for ($i = 0; $i < config('seeder.partner.amount') ; $i++) {
            $partner = factory(Partner::class)->create();
            $partner->save();
            fprintf(STDOUT, "Partner ".$i." \tof ".config('seeder.partner.amount')." created\n");
        }
    }
}
