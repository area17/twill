<?php

if (!function_exists('createDefaultFields')) {
    function createDefaultTableFields($table)
    {
        $table->increments('id');
        $table->softDeletes();
        $table->timestamps();
        $table->boolean('published');
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
        $table->unique(["{$tableNameSingular}_id", 'locale']);
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
