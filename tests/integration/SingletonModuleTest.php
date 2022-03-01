<?php

namespace A17\Twill\Tests\Integration;

use App\Repositories\ContactPageRepository;

class SingletonModuleTest extends TestCase
{
    protected $allFiles = [
        '{$stubs}/singleton/2021_09_30_202102_create_contact_pages_tables.php' => '{$database}/migrations/',
        '{$stubs}/singleton/ContactPage.php' => '{$app}/Models/',
        '{$stubs}/singleton/ContactPageController.php' => '{$app}/Http/Controllers/Admin/',
        '{$stubs}/singleton/ContactPageRepository.php' => '{$app}/Repositories/',
        '{$stubs}/singleton/ContactPageRequest.php' => '{$app}/Http/Requests/Admin/',
        '{$stubs}/singleton/ContactPageRevision.php' => '{$app}/Models/Revisions/',
        '{$stubs}/singleton/ContactPageSlug.php' => '{$app}/Models/Slugs/',
        '{$stubs}/singleton/ContactPageTranslation.php' => '{$app}/Models/Translations/',
        '{$stubs}/singleton/form.blade.php' => '{$resources}/views/admin/contactPages/form.blade.php',
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

        $this->assertSee("ContactPage is not seeded");
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
