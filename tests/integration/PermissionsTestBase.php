<?php

namespace A17\Twill\Tests\Integration;

abstract class PermissionsTestBase extends TestCase
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

        $this->migrate();
    }

    public function loginUser($user)
    {
        $this->loginAs($user->email, $user->email);
    }
}
