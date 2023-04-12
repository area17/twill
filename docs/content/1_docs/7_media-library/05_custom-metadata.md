# Custom metadata

By default, media comes with a few metadata attributes that can be filled in from the media managers: 
Tags, Alt text and caption.

Say we want to add more metadata, some of which might be translatable. To do this we have to go through a few
steps. In the steps below we will add a translatable field for attribution and a non-translatable field for source.

First thing we have to do is add the new metadata fields in the twill settings.

In the twill config (`config/twill.php`) we add the following:

```php
return [
    'media_library' => [
        'extra_metadatas_fields' => [
            [
                'name' => 'source',
                'label' => 'source',
            ],
            [
                'name' => 'attribution',
                'label' => 'attribution',
            ],
        ],
        'translatable_metadatas_fields' => [
            'attribution',
        ],
    ],
];
```

This will already ensure the fields are visible from the media manager. However, saving is not yet possible because the database fields are missing. So to make everything work we have to write a migration.

For regular fields, it is best to use a text field, for translatable fields we suggest to use a json field.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToMedia extends Migration
{
    public function up()
    {
        Schema::table(config('twill.medias_table', 'twill_medias'), function (Blueprint $table) {
            $table->text('source')->nullable();
            $table->json('attribution')->nullable();
        });
    }

    public function down()
    {
        Schema::table(config('twill.medias_table', 'twill_medias'), function (Blueprint $table) {
            $table->dropColumn('source');
            $table->dropColumn('attribution');
        });
    }
}
```

And that is all. The most important part is to make sure your migration fields are named the same as your custom metadata values in the twill config.
