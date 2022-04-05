<?php

namespace A17\Twill\Tests\Integration;

use App\Repositories\ContactPageRepository;

class SingletonModuleTest extends TestCase
{
    public $example = 'tests-singleton';

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function createContactPage()
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

    // FIXME — this is needed for the new admin routes to take effect in the next test,
    // because files are copied in `setUp()` after the app is initialized.
    public function testDummy()
    {
        $this->assertTrue(true);
    }

    public function testSingletonNavigationItem()
    {
        $this->createContactPage();

        $this->httpRequestAssert('/twill', 'GET');

        $this->assertSee('Contact Page');

        $this->assertSee('http://twill.test/twill/contactPage');
    }

    public function testSingletonRouteIsDefined()
    {
        $this->createContactPage();

        $this->httpRequestAssert('/twill/contactPage', 'GET');

        $this->assertSee('This is the ContactPage form');
    }

    public function testSingletonRouteRequiresOneRecord()
    {
        $this->httpRequestAssert('/twill/contactPage', 'GET', [], 500);

        $this->assertSee('ContactPage is not seeded');
    }

    public function testSingletonModuleHasNoIndex()
    {
        $this->createContactPage();

        $this->httpRequestAssert('/twill/contactPages', 'GET', [], 500);

        $this->assertSee('ContactPage has no index');
    }

    public function testSingletonModuleHasStandardRoutes()
    {
        $this->createContactPage();

        $this->httpRequestAssert('/twill/contactPages/1/edit', 'GET');

        $this->assertSee('This is the ContactPage form');
    }
}
