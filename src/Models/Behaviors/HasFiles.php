<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\File;
use A17\Twill\Services\FileLibrary\FileService;

trait HasFiles
{
    public function files()
    {
        return $this->morphToMany(File::class, 'fileable')->withPivot(['role', 'locale'])->withTimestamps();
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

    public function file($role, $locale = null, $file = null)
    {

        if (!$file) {
            $file = $this->findFile($role, $locale);
        }

        if ($file) {
            return FileService::getUrl($file->uuid);
        }

        return null;
    }

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

    public function fileObject($role, $locale = null)
    {
        return $this->findFile($role, $locale);
    }

}
