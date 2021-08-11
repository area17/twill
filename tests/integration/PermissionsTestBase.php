<?php

namespace A17\Twill\Tests\Integration;

use App\Repositories\PostingRepository;
use A17\Twill\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

abstract class PermissionsTestBase extends TestCase
{
    protected $allFiles = [
        // postings module
        '{$stubs}/permissions/postings/2021_07_20_132405_create_postings_tables.php' => '{$database}/migrations/',
        '{$stubs}/permissions/postings/Posting.php' => '{$app}/Models/',
        '{$stubs}/permissions/postings/PostingController.php' => '{$app}/Http/Controllers/Admin/',
        '{$stubs}/permissions/postings/PostingRepository.php' => '{$app}/Repositories/',
        '{$stubs}/permissions/postings/PostingRequest.php' => '{$app}/Http/Requests/Admin/',
        '{$stubs}/permissions/postings/form.blade.php' => '{$resources}/views/admin/postings/',

        // general
        '{$stubs}/permissions/settings/seo.blade.php' => '{$resources}/views/admin/settings/',
        '{$stubs}/permissions/admin.php' => '{$base}/routes/admin.php',
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

        $this->assertSee($user->name);
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
