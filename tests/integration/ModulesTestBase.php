<?php

namespace A17\Twill\Tests\Integration;

use Illuminate\Support\Str;
use App\Models\Translations\AuthorTranslation;
use App\Models\Translations\CategoryTranslation;

abstract class ModulesTestBase extends TestCase
{
    public $name;
    public $name_en;
    public $name_fr;
    public $slug_en;
    public $slug_fr;
    public $description_en;
    public $description_fr;
    public $bio_en;
    public $bio_fr;
    public $birthday;
    public $block_id;
    public $block_editor_name;
    public $block_quote;
    public $translation;
    public $author;
    public $title;
    public $title_en;
    public $title_fr;
    public $category;

    protected $allFiles = [
        '{$stubs}/modules/authors/2019_10_18_193753_create_authors_tables.php' =>
            '{$database}/migrations/',

        '{$stubs}/modules/authors/admin.php' => '{$base}/routes/admin.php',

        '{$stubs}/modules/authors/Author.php' => '{$app}/Models/',

        '{$stubs}/modules/authors/AuthorController.php' =>
            '{$app}/Http/Controllers/Admin/',

        '{$stubs}/modules/authors/AuthorTranslation.php' =>
            '{$app}/Models/Translations/',

        '{$stubs}/modules/authors/AuthorRevision.php' =>
            '{$app}/Models/Revisions/',

        '{$stubs}/modules/authors/AuthorSlug.php' => '{$app}/Models/Slugs/',

        '{$stubs}/modules/authors/AuthorRepository.php' =>
            '{$app}/Repositories/',

        '{$stubs}/modules/authors/AuthorRequest.php' =>
            '{$app}/Http/Requests/Admin/',

        '{$stubs}/modules/authors/form.blade.php' =>
            '{$resources}/views/admin/authors/',

        '{$stubs}/modules/authors/translatable.php' => '{$config}/',

        '{$stubs}/modules/authors/twill-navigation.php' => '{$config}/',

        '{$stubs}/modules/authors/site.blocks.quote.blade.php' =>
            '{$resources}/views/site/blocks/quote.blade.php',

        '{$stubs}/modules/authors/site.layouts.block.blade.php' =>
            '{$resources}/views/site/layouts/block.blade.php',

        // ------------------------------------------

        '{$stubs}/modules/categories/2019_10_24_174613_create_categories_tables.php' =>
            '{$database}/migrations/',

        '{$stubs}/modules/categories/Category.php' => '{$app}/Models/',

        '{$stubs}/modules/categories/CategoryController.php' =>
            '{$app}/Http/Controllers/Admin/',

        '{$stubs}/modules/categories/CategoryTranslation.php' =>
            '{$app}/Models/Translations/',

        '{$stubs}/modules/categories/CategoryRevision.php' =>
            '{$app}/Models/Revisions/',

        '{$stubs}/modules/categories/CategorySlug.php' =>
            '{$app}/Models/Slugs/',

        '{$stubs}/modules/categories/CategoryRepository.php' =>
            '{$app}/Repositories/',

        '{$stubs}/modules/categories/CategoryRequest.php' =>
            '{$app}/Http/Requests/Admin/',

        '{$stubs}/modules/categories/form.blade.php' =>
            '{$resources}/views/admin/categories/',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->copyFiles($this->allFiles);

        $this->loadModulesConfig();

        $this->migrate();

        $this->login();
    }

    protected function assertSomethingWrongHappened()
    {
        $this->assertSee('Something wrong happened!');
    }

    protected function fakeText(int $max = 250)
    {
        /*
         *  #### PHP 7.4 && PHP 8
         *  ## Faker is not yet compatible
         *  ## https://github.com/fzaninotto/Faker/pull/1816/allFiles
         *
         *   As soon as it is fixed, replace it by $this->faker->text($x)
         *
         *  TODO
         */

        $lorem =
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Qualem igitur hominem natura inchoavit? Semovenda est igitur voluptas, non solum ut recta sequamini, sed etiam ut loqui deceat frugaliter. Hoc positum in Phaedro a Platone probavit Epicurus sensitque in omni disputatione id fieri oportere. Summum enim bonum exposuit vacuitatem doloris; Duo Reges: constructio interrete. Tubulo putas dicere? Primum cur ista res digna odio est, nisi quod est turpis? Sed erat aequius Triarium aliquid de dissensione nostra iudicare. Apud ceteros autem philosophos, qui quaesivit aliquid, tacet; Sed quot homines, tot sententiae; Eiuro, inquit adridens, iniquum, hac quidem de re; An eiusdem modi? Nam si beatus umquam fuisset, beatam vitam usque ad illum a Cyro extructum rogum pertulisset. Vestri haec verecundius, illi fortasse constantius. At miser, si in flagitiosa et vitiosa vita afflueret voluptatibus. Quo modo autem philosophus loquitur? Sed ne, dum huic obsequor, vobis molestus sim. Si ad corpus pertinentibus, rationes tuas te video compensare cum istis doloribus, non memoriam corpore perceptarum voluptatum; Stoici autem, quod finem bonorum in una virtute ponunt, similes sunt illorum; Summum enim bonum exposuit vacuitatem doloris; Proclivi currit oratio. Quid in isto egregio tuo officio et tanta fide-sic enim existimo-ad corpus refers? Satis est ad hoc responsum. Confecta res esset. Ac tamen hic mallet non dolere. Quare, quoniam de primis naturae commodis satis dietum est nunc de maioribus consequentibusque videamus. Nec vero sum nescius esse utilitatem in historia, non modo voluptatem. Idem etiam dolorem saepe perpetiuntur, ne, si id non faciant, incidant in maiorem. Scaevola tribunus plebis ferret ad plebem vellentne de ea re quaeri.';

        while (strlen($lorem) < $max) {
            $lorem .= $lorem;
        }

        return substr($lorem, 0, strrpos($lorem, ' ')) . '.';
    }

    public function searchReplaceFile($search, $replace, $file)
    {
        /**
         * Usage
         *
         *      $this->searchReplaceFile(
         *          "'editInModal' => false",
         *          "'editInModal' => true",
         *          twill_path('Http/Controllers/Admin/AuthorController.php')
         *      );
         *
         */
        file_put_contents(
            $file,
            str_replace($search, $replace, file_get_contents($file))
        );
    }

    protected function addBlock()
    {
        $this->httpRequestAssert(
            "/twill/personnel/authors/{$this->author->id}",
            'PUT',
            $this->getUpdateAuthorWithBlock()
        );

        $this->assertEquals(2, $this->author->blocks->count());

        // Check default block content
        $this->assertEquals(
            $block_quote = ['quote' => $this->block_quote],
            $this->author->blocks->first()->content
        );

        // Check named block content
        $this->assertEquals(
            $block_quote = ['quote' => $this->block_quote],
            $this->author->blocks()->named($this->block_editor_name)->get()->first()->content
        );

        // Check if blocks are rendering
        $this->assertEquals(
            clean_file(json_encode($block_quote)),
            clean_file(trim($this->author->renderBlocks()))
        );

        // Check if named blocks are rendering
        $this->assertEquals(
            clean_file(json_encode($block_quote)),
            clean_file(trim($this->author->renderNamedBlocks($this->block_editor_name)))
        );

        // Get browser data
        $this->httpRequestAssert('/twill/personnel/authors/browser');

        $this->assertJson($this->content());

        $data = json_decode($this->content(), true)['data'][0];

        $this->assertEquals(
            $data['edit'],
            "http://twill.test/twill/personnel/authors/{$this->author->id}/edit"
        );

        $this->assertEquals($data['endpointType'], 'App\Models\Author');
    }

    protected function createAuthor($count = 1)
    {
        foreach (range(1, $count) as $c) {
            $this->httpRequestAssert(
                '/twill/personnel/authors',
                'POST',
                $this->getCreateAuthorData()
            );
        }

        $this->translation = AuthorTranslation::where('name', $this->name_en)
            ->where('locale', 'en')
            ->first();

        $this->author = $this->translation->author;

        $this->assertNotNull($this->translation);

        $this->assertCount(3, $this->author->slugs);
    }

    protected function destroyAuthor()
    {
        $this->createAuthor();

        $this->assertNull($this->author->deleted_at);

        $this->httpRequestAssert(
            "/twill/personnel/authors/{$this->author->id}",
            'DELETE',
            $this->getUpdateAuthorWithBlock()
        );

        $this->assertNothingWrongHappened();

        $this->author->refresh();

        $this->assertNotNull($this->author->deleted_at);

        $this->httpRequestAssert(
            '/twill/personnel/authors/9999999',
            'DELETE',
            $this->getUpdateAuthorWithBlock(),
            404
        );
    }

    protected function editAuthor()
    {
        $this->httpRequestAssert(
            "/twill/personnel/authors/{$this->author->id}",
            'PUT',
            $this->getUpdateAuthorData()
        );

        $this->assertNothingWrongHappened();

        $this->author->refresh();

        $this->assertEquals($this->author->birthday, $this->birthday);

        $this->translation->refresh();

        $this->assertEquals(
            $this->description_en,
            $this->translation->description
        );

        $this->assertEquals($this->bio_en, $this->translation->bio);

        $this->assertTrue($this->translation->active);
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
                $this->getAuthorBlock(),
                $this->getAuthorBlock($this->block_editor_name = 'unique-name')
            ],
            'repeaters' => [],
        ];
    }

    public function getAuthorBlock($name = 'default')
    {
        $this->block_quote = $this->block_quote ?? $this->fakeText();

        return [
            'id' => ($this->block_id = rand(
                1570000000000,
                1579999999999
            )),
            'type' => 'a17-block-quote',
            'content' => [
                'quote' => $this->block_quote,
            ],
            'medias' => [],
            'browsers' => [],
            'blocks' => [],
            'name' => $name,
        ];
    }

    /**
     * @return array
     */
    protected function getCreateCategoryData(): array
    {
        $category = $this->title = 'Category: ' . $this->faker->name;

        return [
            'title' => [
                'en' => ($this->title_en = '[EN] ' . $category),
                'fr' => ($this->title_fr = '[FR] ' . $category),
            ],
            'slug' => [
                'en' => ($this->slug_en = Str::slug($this->title_en)),
                'fr' => ($this->slug_fr = Str::slug($this->title_fr)),
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
                    'published' => true,
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

    protected function createCategory($count = 1)
    {
        foreach (range(1, $count) as $c) {
            $this->httpRequestAssert(
                '/twill/categories',
                'POST',
                $this->getCreateCategoryData()
            );
        }

        $this->translation = CategoryTranslation::where(
            'title',
            $this->title_en
        )
            ->where('locale', 'en')
            ->first();

        $this->category = $this->translation->category;

        $this->assertNotNull($this->translation);

        $this->assertCount(3, $this->category->slugs);
    }
}
