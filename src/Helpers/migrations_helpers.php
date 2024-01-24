<?php

use Illuminate\Support\Str;

if (!function_exists('createDefaultFields')) {
    /**
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @param bool $softDeletes
     * @param bool $published
     * @param bool $publishDates
     * @param bool $visibility
     * @return void
     */
    function createDefaultTableFields($table, $softDeletes = true, $published = true, $publishDates = false, $visibility = false)
    {
        $table->bigIncrements('id');

        if ($softDeletes) {
            $table->softDeletes();
        }

        $table->timestamps();

        if ($published) {
            $table->boolean('published')->default(false);
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
    /**
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @param string $tableNameSingular
     * @param string|null $tableNamePlural
     * @return void
     */
    function createDefaultTranslationsTableFields($table, $tableNameSingular, $tableNamePlural = null)
    {
        if (!$tableNamePlural) {
            $tableNamePlural = Str::plural($tableNameSingular);
        }

        $table->bigIncrements('id');
        $table->bigInteger("{$tableNameSingular}_id")->unsigned();

        $table->softDeletes();
        $table->timestamps();
        $table->string('locale', 7)->index();
        $table->boolean('active')->default(false);

        $table->foreign("{$tableNameSingular}_id", "fk_{$tableNameSingular}_translations_{$tableNameSingular}_id")->references('id')->on($tableNamePlural)->onDelete('CASCADE');
        $table->unique(["{$tableNameSingular}_id", 'locale'], "{$tableNameSingular}_id_locale_unique");
    }
}

if (!function_exists('createDefaultSlugsTableFields')) {
    /**
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @param string $tableNameSingular
     * @param string|null $tableNamePlural
     * @return void
     */
    function createDefaultSlugsTableFields($table, $tableNameSingular, $tableNamePlural = null)
    {
        if (!$tableNamePlural) {
            $tableNamePlural = Str::plural($tableNameSingular);
        }

        $table->bigIncrements('id');
        $table->bigInteger("{$tableNameSingular}_id")->unsigned();

        $table->softDeletes();
        $table->timestamps();
        $table->string('slug');
        $table->string('locale', 7)->index();
        $table->boolean('active');
        $table->foreign("{$tableNameSingular}_id", "fk_{$tableNameSingular}_slugs_{$tableNameSingular}_id")->references('id')->on($tableNamePlural)->onDelete('CASCADE')->onUpdate('NO ACTION');
    }
}

if (!function_exists('createDefaultRelationshipTableFields')) {
    /**
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @param string $table1NameSingular
     * @param string $table2NameSingular
     * @param string|null $table1NamePlural
     * @param string|null $table2NamePlural
     * @return void
     */
    function createDefaultRelationshipTableFields($table, $table1NameSingular, $table2NameSingular, $table1NamePlural = null, $table2NamePlural = null)
    {
        if (!$table1NamePlural) {
            $table1NamePlural = Str::plural($table1NameSingular);
        }

        if (!$table2NamePlural) {
            $table2NamePlural = Str::plural($table2NameSingular);
        }

        $table->bigInteger("{$table1NameSingular}_id")->unsigned();
        $table->bigInteger("{$table2NameSingular}_id")->unsigned();

        $table->foreign("{$table1NameSingular}_id")->references('id')->on($table1NamePlural)->onDelete('cascade');
        $table->foreign("{$table2NameSingular}_id")->references('id')->on($table2NamePlural)->onDelete('cascade');
        $table->index(["{$table2NameSingular}_id", "{$table1NameSingular}_id"], "idx_{$table1NameSingular}_{$table2NameSingular}_" . Str::random(5));
    }
}

if (!function_exists('createDefaultRevisionsTableFields')) {
    /**
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @param string $tableNameSingular
     * @param string|null $tableNamePlural
     * @return void
     */
    function createDefaultRevisionsTableFields($table, $tableNameSingular, $tableNamePlural = null)
    {
        if (!$tableNamePlural) {
            $tableNamePlural = Str::plural($tableNameSingular);
        }

        $table->bigIncrements('id');
        $table->bigInteger("{$tableNameSingular}_id")->unsigned();
        $table->bigInteger('user_id')->unsigned()->nullable();

        $table->timestamps();
        $table->json('payload');
        $table->foreign("{$tableNameSingular}_id")->references('id')->on("{$tableNamePlural}")->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on(config('twill.users_table', 'twill_users'))->onDelete('set null');
    }
}
