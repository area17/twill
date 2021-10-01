<?php

namespace A17\Twill\Tests\Integration;

use App\Repositories\ContactpageRepository;

class SingletonModuleTest extends TestCase
{
    protected $allFiles = [
        '{$stubs}/singleton/2021_09_30_202102_create_contactpages_tables.php' => '{$database}/migrations/',
        '{$stubs}/singleton/Contactpage.php' => '{$app}/Models/',
        '{$stubs}/singleton/ContactpageController.php' => '{$app}/Http/Controllers/Admin/',
        '{$stubs}/singleton/ContactpageRepository.php' => '{$app}/Repositories/',
        '{$stubs}/singleton/ContactpageRequest.php' => '{$app}/Http/Requests/Admin/',
        '{$stubs}/singleton/ContactpageRevision.php' => '{$app}/Models/Revisions/',
        '{$stubs}/singleton/ContactpageSlug.php' => '{$app}/Models/Slugs/',
        '{$stubs}/singleton/ContactpageTranslation.php' => '{$app}/Models/Translations/',
        '{$stubs}/singleton/form.blade.php' => '{$resources}/views/admin/contactpages/form.blade.php',
        '{$stubs}/singleton/twill-navigation.php' => '{$config}/',
        '{$stubs}/singleton/admin.php' => '{$base}/routes/admin.php',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->copyFiles($this->allFiles);

        $this->migrate();

        $this->login();
    }

    public function createContactpage()
    {
        app(ContactpageRepository::class)->create([
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
        $this->createContactpage();

        $this->httpRequestAssert('/twill', 'GET');

        $this->assertSee('Contact Page');

        $this->assertSee('http://twill.test/twill/contactpage');
    }

    public function testSingletonRouteIsDefined()
    {
        $this->createContactpage();

        $this->httpRequestAssert('/twill/contactpage', 'GET');

        $this->assertSee('This is the contactpage form');
    }

    public function testSingletonRouteRequiresOneRecord()
    {
        $this->httpRequestAssert('/twill/contactpage', 'GET', [], 500);

        $this->assertSee("Contactpage is missing");
    }

    public function testSingletonModuleHasNoIndex()
    {
        $this->createContactpage();

        $this->httpRequestAssert('/twill/contactpages', 'GET', [], 500);

        $this->assertSee('Contactpage as no index');
    }

    public function testSingletonModuleHasStandardRoutes()
    {
        $this->createContactpage();

        $this->httpRequestAssert('/twill/contactpages/1/edit', 'GET');

        $this->assertSee('This is the contactpage form');
    }
}
