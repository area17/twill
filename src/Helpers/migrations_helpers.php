<?php

if (!function_exists('createDefaultFields')) {
    function createDefaultTableFields($table, $softDeletes = true, $published = true, $publishDates = false, $visibility = false)
    {
        $table->increments('id');

        if ($softDeletes) {
            $table->softDeletes();
        }

        $table->timestamps();

        if ($published) {
            $table->boolean('published');
        }

        if ($publishDates) {
            $table->timestamp('publish_start_date')->nullable();
            $table->timestamp('publish_end_date')->nullable();
        }

        if ($visibility) {
            $table->boolean('public')->default(true);
        }
    }
}

if (!function_exists('createDefaultTranslationsTableFields')) {
    function createDefaultTranslationsTableFields($table, $tableNameSingular, $tableNamePlural = null)
    {
        if (!$tableNamePlural) {
            $tableNamePlural = str_plural($tableNameSingular);
        }

        $table->increments('id');
        $table->softDeletes();
        $table->timestamps();
        $table->string('locale', 6)->index();
        $table->boolean('active');
        $table->integer("{$tableNameSingular}_id")->unsigned();
        $table->foreign("{$tableNameSingular}_id", "fk_{$tableNameSingular}_translations_{$tableNameSingular}_id")->references('id')->on($tableNamePlural)->onDelete('CASCADE');
        $table->unique(["{$tableNameSingular}_id", 'locale'], "{$tableNameSingular}_id_locale_unique");
    }
}

if (!function_exists('createDefaultSlugsTableFields')) {
    function createDefaultSlugsTableFields($table, $tableNameSingular, $tableNamePlural = null)
    {
        if (!$tableNamePlural) {
            $tableNamePlural = str_plural($tableNameSingular);
        }

        $table->increments('id');
        $table->softDeletes();
        $table->timestamps();
        $table->string('slug');
        $table->string('locale', 6)->index();
        $table->boolean('active');
        $table->integer("{$tableNameSingular}_id")->unsigned();
        $table->foreign("{$tableNameSingular}_id", "fk_{$tableNameSingular}_slugs_{$tableNameSingular}_id")->references('id')->on($tableNamePlural)->onDelete('CASCADE')->onUpdate('NO ACTION');
    }
}

if (!function_exists('createDefaultRelationshipTableFields')) {
    function createDefaultRelationshipTableFields($table, $table1NameSingular, $table2NameSingular, $table1NamePlural = null, $table2NamePlural = null)
    {
        if (!$table1NamePlural) {
            $table1NamePlural = str_plural($table1NameSingular);
        }
        if (!$table2NamePlural) {
            $table2NamePlural = str_plural($table2NameSingular);
        }

        $table->integer("{$table1NameSingular}_id")->unsigned();
        $table->foreign("{$table1NameSingular}_id")->references('id')->on($table1NamePlural)->onDelete('cascade');
        $table->integer("{$table2NameSingular}_id")->unsigned();
        $table->foreign("{$table2NameSingular}_id")->references('id')->on($table2NamePlural)->onDelete('cascade');
        $table->index(["{$table2NameSingular}_id", "{$table1NameSingular}_id"], "idx_{$table1NameSingular}_{$table2NameSingular}_" . str_random(5));
    }
}

if (!function_exists('createDefaultRevisionsTableFields')) {
    function createDefaultRevisionsTableFields($table, $tableNameSingular, $tableNamePlural = null)
    {
        if (!$tableNamePlural) {
            $tableNamePlural = str_plural($tableNameSingular);
        }

        $table->increments('id');
        $table->timestamps();
        $table->json('payload');
        $table->integer("{$tableNameSingular}_id")->unsigned()->index();
        $table->integer('user_id')->unsigned()->nullable();
        $table->foreign("{$tableNameSingular}_id")->references('id')->on("{$tableNamePlural}")->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on(config('twill.users_table', 'twill_users'))->onDelete('set null');
    }
}
