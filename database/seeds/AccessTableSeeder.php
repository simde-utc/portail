<?php

use Illuminate\Database\Seeder;
use App\Models\Access;

class AccessTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $access = [
            [
                'type' => 'full_access',
                'name' => 'Total accès',
                'description' => 'Destiné au président du BDE et vice-président chargé de la logistique du BDE, accès total à la MDE sauf Polar',
                'utc_access' => 9
            ],
            [
                'type' => 'polar_extended',
                'name' => 'Polar étendu',
                'description' => 'Accès au Polar + salles de réunion des pôles',
                'utc_access' => 15
            ],
            [
                'type' => 'asso',
                'name' => 'Membre d\'association',
                'description' => 'Accès aux salles de réunion des pôles',
                'utc_access' => 16
            ],
            [
                'type' => 'bde_extended',
                'name' => 'BDE étendu',
                'description' => 'Destiné au bureau du BDE, accès aux salles de réunion des pôles + stockage des pôles + local BDE',
                'utc_access' => 17
            ],
            [
                'type' => 'pae',
                'name' => 'Accès PAE',
                'description' => 'Destiné au responsable PAE du BDE, accès aux salles de réunion des pôles + stockage PAE + salles de musique + studio d\'enregistrement + stockage audiovisuel + local BDE',
                'utc_access' => 18
            ],
            [
                'type' => 'pae_extended',
                'name' => 'PAE étendu',
                'description' => 'Accès aux salles de réunion des pôles + stockage PAE',
                'utc_access' => 19
            ],
            [
                'type' => 'pvdc',
                'name' => 'Accès PVDC',
                'description' => ' 	Destiné au responsable PVDC du BDE, accès au Polar (stockage + réserve) + salles de réunion des pôles + stockage PVDC',
                'utc_access' => 20
            ],
            [
                'type' => 'pvdc_extended',
                'name' => 'PVDC étendu',
                'description' => 'Accès aux salles de réunion des pôles + stockage PVDC',
                'utc_access' => 21
            ],
            [
                'type' => 'psec',
                'name' => 'Accès PSEC',
                'description' => 'Destiné au responsable PSEC du BDE, accès aux salles de réunion des pôles + stockage PTE / PSEC + local EPI / Cac\'Carotte + local BDE',
                'utc_access' => 23
            ],
            [
                'type' => 'polar',
                'name' => 'Accès Polar',
                'description' => 'Accès au Polar (magasin + réserve) + salles de réunion des pôles',
                'utc_access' => 27
            ],
            [
                'type' => 'pte',
                'name' => 'Accès PTE',
                'description' => 'Destiné au responsable PTE du BDE, accès aux salles de réunion des pôles + stockage PTE / PSEC + local USEC + local COMUTEC + local BDE',
                'utc_access' => 34
            ],
            [
                'type' => 'pte_psec_extended',
                'name' => 'PTE/PSEC étendu',
                'description' => 'Accès aux salles de réunion des pôles + stockage PTE / PSEC',
                'utc_access' => 36
            ],
            [
                'type' => 'epi_caccarotte',
                'name' => 'EPI/Cac\'Carotte',
                'description' => 'Destiné aux membres d\'EPI et de Cac\'Carotte uniquement, accès aux salles de réunion des pôles + stockage PTE / PSEC + local EPI / Cac\'Carotte',
                'utc_access' => 38
            ],
            [
                'type' => 'comutec',
                'name' => 'Accès Comutec',
                'description' => 'Destiné aux membres de Comutec, accès aux salles de réunion des pôles + stockage PTE/ PSEC + local COMUTEC',
                'utc_access' => 39
            ],
            [
                'type' => 'lab_photo',
                'name' => 'Labo Photo',
                'description' => 'Destiné à Pics\'art, accès aux salles de réunion des pôles + labo photo',
                'utc_access' => 41
            ],
            [
                'type' => 'veloc',
                'name' => 'Accès Veloc',
                'description' => 'Asso simple + grilles Picasso et BUTC Veloc temporaire',
                'utc_access' => 43
            ],
            [
                'type' => 'usec',
                'name' => 'Accès USEC',
                'description' => 'Destiné aux membres de l\'USEC, accès aux salles de réunion des pôles + stockage PTE / PSEC + local USEC',
                'utc_access' => 48
            ],
            [
                'type' => 'repetition',
                'name' => 'Salle de répétition',
                'description' => 'Destiné aux groupes Larsen, accès aux salles de réunion des pôles + salles de musique',
                'utc_access' => 49
            ],
            [
                'type' => 'decibels',
                'name' => 'Accès Décibels',
                'description' => 'Destiné au bureau de Décibels, accès aux salles de réunion des pôles + salles de musique + studio d\'enregistrement + stockage audiovisuel',
                'utc_access' => 62
            ],
            [
                'type' => 'decibels_ssp',
                'name' => 'Régie Décibels / Local SSP',
                'description' => 'Destiné aux membres de Décibels, accès aux salles de réunion des pôles + salles de musique + studio d\'enregistrement',
                'utc_access' => 72
            ],
            [
                'type' => 'picasso',
                'name' => 'Accès Picasso',
                'description' => 'Accès aux salles de réunion des pôles + pic\'asso porte noire extérieur vers escalier+monte-charge',
                'utc_access' => 93
            ],
        ];

        foreach ($access as $data) {
            Access::create($data);
        }
    }
}
