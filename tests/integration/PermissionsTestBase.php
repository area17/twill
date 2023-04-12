<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\User;
use App\Repositories\PostingRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

abstract class PermissionsTestBase extends TestCase
{
    public ?string $example = 'tests-permissions';

    public function setUp(): void
    {
        parent::setUp();

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
