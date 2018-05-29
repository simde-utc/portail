<?php

use Illuminate\Database\Seeder;
use App\Models\Contact;

class ContactsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contacts = [
            [
                'body' => '01 02 03 04 05',
                'description' => 'Principal',
                'contact_type_id' => 3,
                'visibility_id' => 1,
                'contactable_id' => 1,
                'contactable_type' => Asso::class,
            ],
        ];

        foreach ($contacts as $contact) {
            Contact::create([
                'body' => $contact['body'],
                'description' => $contact['description'],
                'contact_type_id' => $contact['contact_type_id'],
                'visibility_id' => $contact['visibility_id'],
                'contactable_id' => $contact['contactable_id'],
                'contactable_type' => $contact['contactable_type'],
            ]);
        }
    }
}
