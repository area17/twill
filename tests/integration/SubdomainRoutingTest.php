<?php

namespace A17\Twill\Tests\Integration;

class SubdomainRoutingTest extends TestCase
{
    public ?string $example = 'tests-subdomain-routing';

    public function testDummy(): void
    {
        $this->assertTrue(true);
    }

    public function testSubdomainRouting(): void
    {
        $this->assertEquals(
            [
                'subdomain1' => [
                    'pages' => [
                        'title' => 'Pages subdomain 1',
                        'module' => true,
                    ],
                ],
                'subdomain2' => [
                    'pages' => [
                        'title' => 'Pages subdomain 2',
                        'module' => true,
                    ],
                ],
            ],
            config('twill-navigation')
        );

        $this->actingAs($this->superAdmin(), 'twill_users')
            ->get(route('twill.dashboard', ['subdomain' => 'subdomain1']))
            ->assertSee('App 1 name')
            ->assertStatus(200);

        // The second request it would fail because of the config mismatch.
        $this->actingAs($this->superAdmin(), 'twill_users')
            ->get(route('twill.dashboard', ['subdomain' => 'subdomain1']))
            ->assertSee('App 1 name')
            ->assertStatus(200);

        // Check that we can also request subdomain2 within the same runtime.
        $this->actingAs($this->superAdmin(), 'twill_users')
            ->get(route('twill.dashboard', ['subdomain' => 'subdomain2']))
            ->assertSee('App 2 name')
            ->assertStatus(200);
    }

    public function testExceptionIsThrownWhenSubdomainIsNotFoundInNavigation(): void
    {
        $this->actingAs($this->superAdmin(), 'twill_users')
            //  We use json here to easily assert. In browsers this would display depending on the exception handler.
            ->getJson(route('twill.dashboard', ['subdomain' => 'nonExistingDomain']))
            ->assertJsonPath('message', 'Subdomain: nonexistingdomain')
            ->assertJsonPath('exception', 'A17\Twill\Exceptions\NoNavigationForSubdomainException')
            ->assertStatus(500);
    }
}
