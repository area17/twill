<?php

namespace A17\Twill\Tests\Integration;

use App\Repositories\ContactPageRepository;

class SingletonModuleTest extends TestCase
{
    public ?string $example = 'tests-singleton';

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function createContactPage(): void
    {
        app(ContactPageRepository::class)->create([
            'title' => [
                'en' => 'Lorem ipsum',
                'fr' => 'Nullam elementum',
            ],
            'description' => [
                'en' => 'Lorem ipsum dolor sit amet',
                'fr' => 'Nullam elementum sed velit',
            ],
            'published' => true,
        ]);
    }

    public function testSingletonNavigationItem(): void
    {
        $this->createContactPage();

        $this->httpRequestAssert('/twill', 'GET');

        $this->assertSee('Contact Page');

        $this->assertSee('http://twill.test/twill/contactPage');
    }

    public function testSingletonRouteIsDefined(): void
    {
        $this->createContactPage();

        $this->httpRequestAssert('/twill/contactPage', 'GET');

        $this->assertSee('This is the ContactPage form');
    }

    public function testSingletonRouteAutoSeeds(): void
    {
        $this->httpRequestAssert('/twill/contactPage', 'GET', [], 200);
        $this->assertDontSee("ContactPage is not seeded");
    }

    public function testSingletonRouteRequiresOneRecordIfNotAutoSeeded(): void
    {
        $this->app->get('config')->set('twill.auto_seed_singletons', false);
        $this->httpRequestAssert('/twill/contactPage', 'GET', [], 500);

        $this->assertSee('ContactPage is not seeded');
    }

    public function testSingletonModuleHasNoIndex(): void
    {
        $this->createContactPage();

        $this->httpRequestAssert('/twill/contactPages', 'GET', [], 500);

        $this->assertSee('ContactPage has no index');
    }

    public function testSingletonModuleHasStandardRoutes(): void
    {
        $this->createContactPage();

        $this->httpRequestAssert('/twill/contactPage', 'GET');

        $this->assertSee('This is the ContactPage form');
    }
}
