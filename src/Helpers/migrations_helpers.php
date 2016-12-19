<?php

if (!function_exists('createDefaultFields')) {
    function createDefaultTableFields($table)
    {
        $table->increments('id');
        $table->softDeletes();
        $table->timestamps();
    }
}

if (!function_exists('createDefaultTranslationsTableFields')) {
    function createDefaultTranslationsTableFields($table, $tableNameSingular)
    {
        createDefaultTableFields($table);
        $table->string('locale', 6)->index();
        $table->boolean('active');
        $table->integer("$tableNameSingular_id")->unsigned();
        $table->foreign("$tableNameSingular_id", "fk_$tableNameSingular_translations_$tableNameSingular_id")->references('id')->on($table)->onDelete('CASCADE');
        $table->unique(["$tableNameSingular_id", 'locale']);
    }
}

if (!function_exists('createDefaultSlugsTableFields')) {
    function createDefaultSlugsTableFields($table, $tableNameSingular)
    {
        createDefaultTableFields($table);
        $table->string('slug');
        $table->string('locale', 6)->index();
        $table->boolean('active');
        $table->integer("$tableNameSingular_id")->unsigned();
        $table->foreign("$tableNameSingular_id", "fk_$tableNameSingular_translations_$tableNameSingular_id")->references('id')->on($table)->onDelete('CASCADE')->onUpdate('NO ACTION');
    }
}
