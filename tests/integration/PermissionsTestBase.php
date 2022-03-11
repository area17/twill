<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\User;
use App\Repositories\PostingRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

abstract class PermissionsTestBase extends TestCase
{
    protected $allFiles = [
        // postings module
        '{$stubs}/permissions/postings/2021_07_20_132405_create_postings_tables.php' => '{$database}/migrations/',
        '{$stubs}/permissions/postings/Posting.php' => '{$app}/Models/',
        '{$stubs}/permissions/postings/PostingController.php' => '{$app}/Http/Controllers/Twill/',
        '{$stubs}/permissions/postings/PostingRepository.php' => '{$app}/Repositories/',
        '{$stubs}/permissions/postings/PostingRequest.php' => '{$app}/Http/Requests/Twill/',
        '{$stubs}/permissions/postings/form.blade.php' => '{$resources}/views/twill/postings/',

        // general
        '{$stubs}/permissions/settings/seo.blade.php' => '{$resources}/views/twill/settings/',
        '{$stubs}/permissions/admin.php' => '{$base}/routes/twill.php',
        '{$stubs}/permissions/translatable.php' => '{$config}/',
        '{$stubs}/permissions/twill-navigation.php' => '{$config}/',
        '{$stubs}/permissions/twill.php' => '{$config}/',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->copyFiles($this->allFiles);

        $this->migrate();

        Mail::fake();
    }

    public function loginUser($user)
    {
        $this->loginAs($user->email, $user->email);

        $this->httpRequestAssert('/twill');

        $this->assertSee('Logout');
    }

    public function makeUser()
    {
        $user = User::make([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'published' => true,
        ]);

        $user->password = Hash::make($user->email);

        return $user;
    }

    public function createPosting()
    {
        return app(PostingRepository::class)->create([
            'title' => $this->faker->name,
            'published' => true,
        ]);
    }
}
