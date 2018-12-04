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

    public function file($role, $locale = null, $file = null)
    {
        $locale = $locale ?? app()->getLocale();

        if (!$file) {
            $file = $this->files->first(function ($file) use ($role, $locale) {
                $localeScope = ($locale === 'fallback') ? true : ($file->pivot->locale === $locale);
                return $file->pivot->role === $role && $localeScope;
            });
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
        $locale = $locale ?? app()->getLocale();

        return $this->files->first(function ($file, $key) use ($role, $locale) {
            return $file->pivot->role === $role && $file->pivot->locale === $locale;
        });
    }

}
