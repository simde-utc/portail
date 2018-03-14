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
            'pattern' => '/[a-zA-Z0-9_-.+]+@[a-zA-Z0-9-]+.[a-zA-Z]+/',
            'max' => 31,
            'visibility_id' => 'public',
          ],
          [
            'name' => 'Url',
            'pattern' => '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#iS',
            'max' => 2,
            'visibility_id' => 'public',
          ],
          [
            'name' => 'Numéro de téléphone',
            'pattern' => '/[^0-9]/',
            'max' => 2,
            'visibility_id' => 'contributor',
          ],
          [
            'name' => 'Facebook',
            'pattern' => '/(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[?\w\-]*\/)?(?:profile.php\?id=(?=\d.*))?([\w\-]*)?/',
            'max' => 1,
            'visibility_id' => 'public',
          ],
          [
            'name' => 'Twitter',
            'pattern' => '/(https?:)?\/\/(www\.)?twitter.com\/(#!\/)?([^\/ ].)+/',
            'max' => 1,
            'visibility_id' => 'public',
          ],
          [
            'name' => 'LinkedIn',
            'pattern' => '/((http(s?)://)*([a-zA-Z0-9\-])*\.|[linkedin])[linkedin/~\-]+\.[a-zA-Z0-9/~\-_,&=\?\.;]+[^\.,\s<]+/',
            'max' => 1,
            'visibility_id' => 'public',
          ],
        ];

        foreach ($types as $type) {
            $type['visibility_id'] = Visibility::where([
              'type' => $type['visibility_id']
            ])->first()->id;

            ContactType::create($type);
        }
    }
}
