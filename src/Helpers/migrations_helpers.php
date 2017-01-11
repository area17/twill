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
    function createDefaultTranslationsTableFields($table, $tableNameSingular)
    {
        createDefaultTableFields($table);
        $table->string('locale', 6)->index();
        $table->boolean('active');
        $table->integer("{$tableNameSingular}_id")->unsigned();
        $table->foreign("{$tableNameSingular}_id", "fk_{$tableNameSingular}_translations_{$tableNameSingular}_id")->references('id')->on($table)->onDelete('CASCADE');
        $table->unique(["{$tableNameSingular}_id", 'locale']);
    }
}

if (!function_exists('createDefaultSlugsTableFields')) {
    function createDefaultSlugsTableFields($table, $tableNameSingular)
    {
        createDefaultTableFields($table);
        $table->string('slug');
        $table->string('locale', 6)->index();
        $table->boolean('active');
        $table->integer("{$tableNameSingular}_id")->unsigned();
        $table->foreign("{$tableNameSingular}_id", "fk_{$tableNameSingular}_translations_{$tableNameSingular}_id")->references('id')->on($table)->onDelete('CASCADE')->onUpdate('NO ACTION');
    }
}
