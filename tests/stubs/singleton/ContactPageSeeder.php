<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Repositories\ContactPageRepository;
use App\Models\ContactPage;

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
                'en' => 'Homepage',
                // add other languages here
            ],
            'description' => [
                'en' => '',
                // add other languages here
            ],
            'published' => false,
        ]);
    }
}
