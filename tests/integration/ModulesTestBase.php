<?php

namespace A17\Twill\Tests\Integration;

abstract class ModulesTestBase extends TestCase
{
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

        $this->loadConfig();

        $this->migrate();

        $this->login();
    }

    protected function loadConfig()
    {
        $config = require $this->makeFileName(
            '{$stubs}/modules/authors/twill.php'
        );

        config(['twill' => $config + config('twill')]);
    }

    /**
     * Migrate database.
     */
    public function migrate()
    {
        $this->artisan('migrate');
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
}
