<?php

namespace A17\Twill\Tests\Integration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use App\Models\Revisions\AuthorRevision;
use App\Models\Translations\AuthorTranslation;

class ModulesTest extends TestCase
{
    protected $name;
    protected $name_en;
    protected $name_fr;
    protected $slug_en;
    protected $slug_fr;
    protected $description_en;
    protected $description_fr;
    protected $bio_en;
    protected $bio_fr;
    protected $birthday;
    protected $block_id;
    protected $block_quote;

    private $authorFiles = [
        '{$stubs}/modules/authors/2019_10_18_193753_create_authors_tables.php' =>
            '{$database}/migrations/2019_10_18_193753_create_authors_tables.php',

        '{$stubs}/modules/authors/admin.php' => '{$base}/routes/admin.php',

        '{$stubs}/modules/authors/Author.php' => '{$app}/Models/Author.php',

        '{$stubs}/modules/authors/AuthorController.php' =>
            '{$app}/Http/Controllers/Admin/AuthorController.php',

        '{$stubs}/modules/authors/AuthorTranslation.php' =>
            '{$app}/Models/Translations/AuthorTranslation.php',

        '{$stubs}/modules/authors/AuthorRevision.php' =>
            '{$app}/Models/Revisions/AuthorRevision.php',

        '{$stubs}/modules/authors/AuthorSlug.php' =>
            '{$app}/Models/Slugs/AuthorSlug.php',

        '{$stubs}/modules/authors/AuthorRepository.php' =>
            '{$app}/Repositories/AuthorRepository.php',

        '{$stubs}/modules/authors/AuthorRequest.php' =>
            '{$app}/Http/Requests/Admin/AuthorRequest.php',

        '{$stubs}/modules/authors/form.blade.php' =>
            '{$resources}/views/admin/authors/form.blade.php',

        '{$stubs}/modules/authors/translatable.php' =>
            '{$config}/translatable.php',

        '{$stubs}/modules/authors/twill-navigation.php' =>
            '{$config}/twill-navigation.php',

        '{$stubs}/modules/authors/site.blocks.quote.blade.php' =>
            '{$resources}/views/site/blocks/quote.blade.php',

        '{$stubs}/modules/authors/site.layouts.block.blade.php' =>
            '{$resources}/views/site/layouts/block.blade.php',
    ];

    private function loadConfig()
    {
        $config = require $this->makeFileName(
            '{$stubs}/modules/authors/twill.php'
        );

        config(['twill' => $config + config('twill')]);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->copyFiles($this->authorFiles);

        $this->loadConfig();

        $this->migrate();

        $this->login();
    }

    private function fakeText(int $max = 250)
    {
        /*
         *  #### PHP 7.4 && PHP 8
         *  ## Faker is not yet compatible
         *  ## https://github.com/fzaninotto/Faker/pull/1816/files
         *
         *   As soon as it is fixed, replace it by $this->faker->text($x)
         */

        $lorem =
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Qualem igitur hominem natura inchoavit? Semovenda est igitur voluptas, non solum ut recta sequamini, sed etiam ut loqui deceat frugaliter. Hoc positum in Phaedro a Platone probavit Epicurus sensitque in omni disputatione id fieri oportere. Summum enim bonum exposuit vacuitatem doloris; Duo Reges: constructio interrete. Tubulo putas dicere? Primum cur ista res digna odio est, nisi quod est turpis? Sed erat aequius Triarium aliquid de dissensione nostra iudicare. Apud ceteros autem philosophos, qui quaesivit aliquid, tacet; Sed quot homines, tot sententiae; Eiuro, inquit adridens, iniquum, hac quidem de re; An eiusdem modi? Nam si beatus umquam fuisset, beatam vitam usque ad illum a Cyro extructum rogum pertulisset. Vestri haec verecundius, illi fortasse constantius. At miser, si in flagitiosa et vitiosa vita afflueret voluptatibus. Quo modo autem philosophus loquitur? Sed ne, dum huic obsequor, vobis molestus sim. Si ad corpus pertinentibus, rationes tuas te video compensare cum istis doloribus, non memoriam corpore perceptarum voluptatum; Stoici autem, quod finem bonorum in una virtute ponunt, similes sunt illorum; Summum enim bonum exposuit vacuitatem doloris; Proclivi currit oratio. Quid in isto egregio tuo officio et tanta fide-sic enim existimo-ad corpus refers? Satis est ad hoc responsum. Confecta res esset. Ac tamen hic mallet non dolere. Quare, quoniam de primis naturae commodis satis dietum est nunc de maioribus consequentibusque videamus. Nec vero sum nescius esse utilitatem in historia, non modo voluptatem. Idem etiam dolorem saepe perpetiuntur, ne, si id non faciant, incidant in maiorem. Scaevola tribunus plebis ferret ad plebem vellentne de ea re quaeri.';

        while (strlen($lorem) < $max) {
            $lorem .= $lorem;
        }

        return substr($lorem, 0, strrpos($lorem, ' ')) . '.';
    }

    /**
     * @return array
     */
    protected function getCreateAuthorData(): array
    {
        $name = $this->name = $this->faker->name;

        return [
            'name' => [
                'en' => ($this->name_en = '[EN] ' . $name),
                'fr' => ($this->name_fr = '[FR] ' . $name),
            ],
            'slug' => [
                'en' => ($this->slug_en = Str::slug($this->name_en)),
                'fr' => ($this->slug_fr = Str::slug($this->name_fr)),
            ],
            'published' => false,
            'languages' => [
                [
                    'shortlabel' => 'EN',
                    'label' => 'English',
                    'value' => 'en',
                    'disabled' => false,
                    'published' => true,
                ],
                [
                    'shortlabel' => 'FR',
                    'label' => 'French',
                    'value' => 'fr',
                    'disabled' => false,
                    'published' => false,
                ],
                [
                    'shortlabel' => 'PT-BR',
                    'label' => 'pt-BR',
                    'value' => 'pt-BR',
                    'disabled' => false,
                    'published' => false,
                ],
            ],
        ];
    }

    public function getUpdateAuthorData()
    {
        return [
            'name' => [
                'en' => $this->name_en,
                'fr' => $this->name_fr,
                'pt-BR' => '',
            ],
            'slug' => [
                'en' => $this->slug_en,
                'fr' => $this->slug_en,
                'pt-BR' => $this->slug_en,
            ],
            'description' => [
                'en' => ($this->description_en = '[EN] ' . $this->fakeText(80)),
                'fr' => ($this->description_fr = '[FR] ' . $this->fakeText(80)),
                'pt-BR' => '',
            ],
            'birthday' => ($this->birthday = now()->format('Y-m-d')),
            'bio' => [
                'en' => ($this->bio_en = '[EN] ' . $this->fakeText(255)),
                'fr' => ($this->bio_fr = '[FR] ' . $this->fakeText(255)),
                'pt-BR' => '',
            ],
            'cmsSaveType' => 'save',
            'published' => false,
            'public' => false,
            'publish_start_date' => null,
            'publish_end_date' => null,
            'languages' => [
                [
                    'shortlabel' => 'EN',
                    'label' => 'English',
                    'value' => 'en',
                    'disabled' => false,
                    'published' => '1',
                ],
                [
                    'shortlabel' => 'FR',
                    'label' => 'French',
                    'value' => 'fr',
                    'disabled' => false,
                    'published' => '0',
                ],
                [
                    'shortlabel' => 'PT-BR',
                    'label' => 'pt-BR',
                    'value' => 'pt-BR',
                    'disabled' => false,
                    'published' => '0',
                ],
            ],
            'parent_id' => 0,
            'medias' => [],
            'browsers' => [],
            'repeaters' => [],
        ];
    }

    public function getUpdateAuthorWithBlock()
    {
        return $this->getUpdateAuthorData() + [
            'blocks' => [
                [
                    'id' => ($this->block_id = rand(
                        1570000000000,
                        1579999999999
                    )),
                    'type' => 'a17-block-quote',
                    'content' => [
                        'quote' => ($this->block_quote = $this->fakeText()),
                    ],
                    'medias' => [],
                    'browsers' => [],
                    'blocks' => [],
                ],
            ],
            'repeaters' => [],
        ];
    }

    public function testCanCopyFiles()
    {
        collect($this->authorFiles)->each(function ($destination) {
            $this->assertFileExists($this->makeFileName($destination));
        });
    }

    public function testCanMigrateDatabase()
    {
        $this->assertTrue(Schema::hasTable('authors'));
        $this->assertTrue(Schema::hasTable('author_translations'));
        $this->assertTrue(Schema::hasTable('author_slugs'));
        $this->assertTrue(Schema::hasTable('author_revisions'));
    }

    public function testCanDisplayModuleInNavigation()
    {
        $this->request('/twill');

        $this->assertStringContainsString('Personnel', $this->content());

        $this->request('/twill/personnel/authors');

        $this->assertStringContainsString('Name', $this->content());
        $this->assertStringContainsString('Languages', $this->content());
        $this->assertStringContainsString('Mine', $this->content());
        $this->assertStringContainsString('Add new', $this->content());
    }

    public function testCanCreateAnAuthor()
    {
        // Create author (name)
        $this->request(
            '/twill/personnel/authors',
            'POST',
            $this->getCreateAuthorData()
        )->assertStatus(200);

        $authorTranslation = AuthorTranslation::where('name', $this->name_en)
            ->where('locale', 'en')
            ->first();

        $this->assertNotNull($authorTranslation);

        $this->assertCount(3, $authorTranslation->author->slugs);

        // Edit author additional fields
        $this->request(
            "/twill/personnel/authors/{$authorTranslation->author->id}",
            'PUT',
            $this->getUpdateAuthorData()
        )->assertStatus(200);

        $authorTranslation->author->refresh();

        $this->assertEquals(
            $authorTranslation->author->birthday,
            $this->birthday
        );

        $authorTranslation->refresh();

        $this->assertEquals(
            $authorTranslation->description,
            $this->description_en
        );

        // Add one block
        $this->request(
            "/twill/personnel/authors/{$authorTranslation->author->id}",
            'PUT',
            $this->getUpdateAuthorWithBlock()
        )->assertStatus(200);

        $this->assertEquals(1, $authorTranslation->author->blocks->count());

        $this->assertEquals(
            $block_quote = ['quote' => $this->block_quote],
            $authorTranslation->author->blocks->first()->content
        );

        // Check if blocks are rendering
        $this->assertEquals(
            clean_file(json_encode($block_quote)),
            clean_file($authorTranslation->author->renderBlocks())
        );

        // Get browser data
        $this->request('/twill/personnel/authors/browser')->assertStatus(200);

        $this->assertJson($this->content());

        $data = json_decode($this->content(), true)['data'][0];

        $this->assertEquals(
            $data['edit'],
            "http://twill.test/twill/personnel/authors/{$authorTranslation->author->id}/edit"
        );
        $this->assertEquals($data['endpointType'], 'App\Models\Author');

        // Test author redirection to edit
        $this->request(
            "/twill/personnel/authors/{$authorTranslation->author->id}"
        )->assertStatus(200);

        $this->assertStringContainsString(
            clean_file($this->description_en),
            clean_file($this->content())
        );

        // Test preview

        // Check revisions
        $this->assertCount(3, $revisions = AuthorRevision::all());

        // http://twill.test/twill/personnel/authors/restoreRevision/{id}
    }

    public function testCanViewPreview()
    {
        $data = [
            'id' => 1,
            'type' => 'a17-block-quote',
            'content' => [
                'quote' => ($quote = $this->fakeText(70)),
            ],
            'medias' => [],
            'browsers' => [],
            'blocks' => [],
            'activeLanguage' => 'en',
        ];

        $this->request('/twill/blocks/preview', 'POST', $data)->assertStatus(
            200
        );

        $this->assertStringContainsString(
            clean_file(json_encode(['quote' => $quote])),
            clean_file($this->content())
        );
    }
}
