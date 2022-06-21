### Generating a new test on twill-app and moving it to twill package

```
cd /code/twill-app
a twill:module categories --hasBlocks --hasTranslation --hasSlug --hasMedias --hasFiles --hasPosition --hasRevisions

cd /code/twill
mkdir -p tests/stubs/modules/categories

cp $HOME/code/area17/twill-app/database/migrations/2019_10_24_174613_create_categories_tables.php tests/stubs/modules/categories
cp $HOME/code/area17/twill-app/app/Models/Category.php tests/stubs/modules/categories
cp $HOME/code/area17/twill-app/app/Http/Controllers/Twill/CategoryController.php tests/stubs/modules/categories
cp $HOME/code/area17/twill-app/app/Models/Translations/CategoryTranslation.php tests/stubs/modules/categories
cp $HOME/code/area17/twill-app/app/Models/Revisions/CategoryRevision.php tests/stubs/modules/categories
cp $HOME/code/area17/twill-app/app/Models/Slugs/CategorySlug.php tests/stubs/modules/categories
cp $HOME/code/area17/twill-app/app/Repositories/CategoryRepository.php tests/stubs/modules/categories
cp $HOME/code/area17/twill-app/app/Http/Requests/Twill/CategoryRequest.php tests/stubs/modules/categories
cp $HOME/code/area17/twill-app/resources/views/twill/categories/form.blade.php tests/stubs/modules/categories/
cp $HOME/code/area17/twill-app/config/translatable.php tests/stubs/modules/categories
cp $HOME/code/area17/twill-app/config/twill-navigation.php tests/stubs/modules/categories
cp tests/stubs/modules/authors/site.author.blade.php tests/stubs/modules/categories/site.category.blade.php
cp tests/stubs/modules/authors/site.blocks.quote.blade.php tests/stubs/modules/categories
cp tests/stubs/modules/authors/site.layouts.block.blade.php tests/stubs/modules/categories
cp tests/stubs/modules/authors/twill.php tests/stubs/modules/categories
cp tests/stubs/modules/authors/translatable.php tests/stubs/modules/categories
cp tests/stubs/modules/authors/translatable.php tests/stubs/modules/categories
cp tests/stubs/modules/authors/twill-navigation.php tests/stubs/modules/categories
cp tests/stubs/modules/authors/twill.php tests/stubs/modules/categories
```
