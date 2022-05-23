<?php

namespace Database\Seeders;

use App\Models\ContactPage;
use App\Repositories\ContactPageRepository;
use Illuminate\Database\Seeder;

class ContactPageSeeder extends Seeder
{
    /**
     * Create the database record for this singleton module.
     *
     * @return void
     */
    public function run()
    {
        if (ContactPage::count() > 0) {
            return;
        }

        app(ContactPageRepository::class)->create([
            'title' => [
                'en' => 'Lorem ipsum',
                'fr' => 'Nullam elementum',
            ],
            'description' => [
                'en' => 'Lorem ipsum dolor sit amet',
                'fr' => 'Nullam elementum sed velit',
            ],
            'active' => [
                'en' => true,
                'fr' => true,
            ],
            'published' => true,
        ]);
    }
}
