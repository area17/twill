<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\File;
use A17\Twill\Services\FileLibrary\FileService;

trait HasFiles
{
    /**
     * Defines the many-to-many relationship for file objects.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function files()
    {
        return $this->morphToMany(
            File::class,
            'fileable',
            config('twill.fileables_table', 'twill_fileables')
        )->withPivot(['role', 'locale'])
            ->withTimestamps()->orderBy(config('twill.fileables_table', 'twill_fileables') . '.id', 'asc');
    }

    private function findFile($role, $locale)
    {
        $locale = $locale ?? app()->getLocale();

        $file = $this->files->first(function ($file) use ($role, $locale) {
            return $file->pivot->role === $role && $file->pivot->locale === $locale;
        });

        if (!$file && config('translatable.use_property_fallback', false)) {
            $file = $this->files->first(function ($file) use ($role) {
                return $file->pivot->role === $role && $file->pivot->locale === config('translatable.fallback_locale');
            });
        }

        return $file;
    }

    /**
     * Returns the URL of the attached file for a role.
     *
     * @param string $role Role name.
     * @param string|null $locale Locale of the file if your site has multiple languages.
     * @param File|null $file Provide a file object if you already retrieved one to prevent more SQL queries.
     * @return string|null
     */
    public function file($role, $locale = null, $file = null)
    {

        if ($file === null) {
            $file = $this->findFile($role, $locale);
        }

        if ($file) {
            return FileService::getUrl($file->uuid);
        }

        return null;
    }

    /**
     * Returns an array of URLs of all attached files for a role.
     *
     * @param string $role Role name.
     * @param string|null $locale Locale of the file if your site has multiple languages.
     * @return array
     */
    public function filesList($role, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        $files = $this->files->filter(function ($file) use ($role, $locale) {
            return $file->pivot->role === $role && $file->pivot->locale === $locale;
        });

        $urls = [];

        foreach ($files as $file) {
            $urls[] = $this->file($role, $locale, $file);
        }

        return $urls;
    }

    /**
     * Returns the file object attached for a role.
     *
     * @param string $role Role name.
     * @param string|null $locale Locale of the file if your site has multiple languages.
     * @return File|null
     */
    public function fileObject($role, $locale = null)
    {
        return $this->findFile($role, $locale);
    }
}
