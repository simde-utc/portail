<?php

use Illuminate\Database\Seeder;
use App\Models\ContactType;
use App\Models\Visibility;

class ContactsTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            [
                'name' => 'Adresse email',
                'type' => 'email',
                'pattern' => '[a-zA-Z0-9_\-.+]+@[a-zA-Z0-9-]+.[a-zA-Z]+',
            ],
            [
                'name' => 'Url',
                'type' => 'url',
                'pattern' => '\b(([\w-]+:\\/\\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\\/)))',
            ],
            [
                'name' => 'Numéro de téléphone',
                'type' => 'phone',
                'pattern' => '\+?[^0-9 \.]*',
            ],
            [
                'name' => 'Facebook',
                'type' => 'facebook',
                'pattern' => '(?:(?:http|https):\\/\\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[?\w\-]*\/)?(?:profile.php\?id=(?=\d.*))?([\w\-]*)?',
            ],
            [
                'name' => 'Twitter',
                'type' => 'twitter',
                'pattern' => '(https?:)?\\/\\/(www\.)?twitter.com\/(#!\/)?([^\/ ].)+',
            ],
            [
                'name' => 'LinkedIn',
                'type' => 'linkedin',
                'pattern' => '((http(s?)://)*([a-zA-Z0-9\-])*\.|[linkedin])[linkedin/~\-]+\.[a-zA-Z0-9/~\-_,&=\?\.;]+[^\.,\s<]+',
            ],
            [
                'name' => 'Autre',
                'type' => 'other',
                'pattern' => '.*',
            ],
        ];

        foreach ($types as $type) {
            ContactType::create($type);
        }
    }
}
