<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Repositories\AboutRepository;
use App\Models\About;

class AboutSeeder extends Seeder
{
    /**
     * Create the database record for this singleton module.
     *
     * @return void
     */
    public function run()
    {
        if (About::count() > 0) {
            return;
        }

        app(AboutRepository::class)->create([
            'title' => [
                'en' => 'About',
                // add other languages here
            ],
            'text' => [
                'en' => '',
                // add other languages here
            ],
            'published' => false,
        ]);
    }
}
