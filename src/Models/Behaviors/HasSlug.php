<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Facades\TwillCapsules;
use A17\Twill\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/** @property Collection<Model> $slugs */
trait HasSlug
{
    private int $nb_variation_slug = 3;
    public ?array $twillSlugData = null;

    private bool $twill_restoring = false;

    protected static function bootHasSlug(): void
    {
        static::restoring(function (self $model) {
            $model->restoreSlugs();
            $model->twill_restoring = true;
        });

        static::saving(function (self $model) {
            if (!$model->twill_restoring && !isset($model->twillSlugData)) {
                // Run this before saving because we need to know which fields are dirty
                $model->twillSlugData = $model->getSlugParams(null, true);
            }
        });

        static::saved(function (self $model) {
            if (!$model->twill_restoring) {
                $model->handleSlugsSaving();
                $model->twillSlugData = null;
            }
            $model->twill_restoring = false;
        });

        static::deleting(function (self $model) {
            $model->slugs()->delete();
        });
    }

    /**
     * Defines the one-to-many relationship for slug objects.
     */
    public function slugs(): HasMany
    {
        return $this->hasMany($this->getSlugModelClass());
    }

    /**
     * Returns an instance of the slug class for this model.
     *
     * @return object
     */
    public function getSlugClass(): Model
    {
        return new ($this->getSlugModelClass());
    }

    /**
     * Returns the fully qualified slug class name for this model.
     */
    public function getSlugModelClass(): string
    {
        return TwillCapsules::guessRelatedModelClass('Slug', $this);
    }

    protected function getSlugClassName(): string
    {
        return class_basename($this) . "Slug";
    }

    public function scopeForSlug(Builder $query, string $slug): Builder
    {
        return $query->whereHas('slugs', function ($query) use ($slug) {
            $query->whereSlug($slug);
            $query->whereActive(true);
            $query->whereLocale(app()->getLocale());
        })->with(['slugs']);
    }

    public function scopeForInactiveSlug(Builder $query, string $slug): Builder
    {
        return $query->whereHas('slugs', function ($query) use ($slug) {
            $query->whereSlug($slug);
            $query->whereLocale(app()->getLocale());
        })->with(['slugs']);
    }

    public function scopeForFallbackLocaleSlug(Builder $query, string $slug): Builder
    {
        return $query->whereHas('slugs', function ($query) use ($slug) {
            $query->whereSlug($slug);
            $query->whereActive(true);
            $query->whereLocale(config('translatable.fallback_locale'));
        })->with(['slugs']);
    }

    public function setSlugs(): void
    {
        foreach ($this->getSlugParams() as $slugParams) {
            $this->updateOrNewSlug($slugParams);
        }
    }

    public function restoreSlugs(): void
    {
        $activeSlugs = $this->slugs()->withTrashed()->get();

        $hasActive = false;
        $activeSlugs->each(function ($slug) use (&$hasActive) {
            if ($slug->active) {
                $hasActive = true;
                $slug->slug = $this->suffixSlugIfExisting(['locale' => $slug->locale, 'slug' => $slug->slug]);
            }
            $slug->deleted_at = null;
            $slug->save();
        });

        if (!$hasActive) {
            $this->setSlugs();
        }
    }

    /**
     * @deprecated This method should not be used directly and will be removed in 4.x use $model->save() instead
     */
    public function handleSlugsOnSave(): void
    {
        $this->handleSlugsSaving();
    }

    private function handleSlugsSaving(): void
    {
        if (!isset($this->twillSlugData)) {
            $slugParams = $this->getSlugParams(null, true);
        } else {
            $slugParams = $this->twillSlugData;

            foreach ($slugParams as $locale => $params) {
                $slugParams[$locale] = array_merge($this->getSlugParams($locale, empty($params['slug'])) ?? [], $params);
            }
            $slugParams = array_filter($slugParams, fn($p) => !empty($p['slug']));
        }

        foreach ($slugParams as $params) {
            $this->updateOrNewSlug($params);
        }
    }

    public function updateOrNewSlug(array $slugParams): void
    {
        if (in_array($slugParams['locale'], config('twill.slug_utf8_languages', []))) {
            $slugParams['slug'] = $this->getUtf8Slug($slugParams['slug']);
        } else {
            $slugParams['slug'] = Str::slug($slugParams['slug']);
        }

        if (empty($slugParams['slug'])) {
            return;
        }
        $slugParams['slug'] = $this->suffixSlugIfExisting($slugParams);
        $oldMatchingSlug = $this->getExistingSlug($slugParams, true);

        // Active old slug if already existing or create a new one.
        if ($oldMatchingSlug) {
            $isNowActive = (bool)($slugParams['active'] ?? false);
            if ($oldMatchingSlug->active != $isNowActive) {
                $this->slugs()->whereKey($oldMatchingSlug->getKey())->update(['active' => $isNowActive]);
                if ($this->relationLoaded('slugs')) {
                    // Report update to slugs so that getSlug() returns the correct value
                    $slug = $this->slugs->where($this->slugs()->getModel()->getKeyName(), $oldMatchingSlug->getKey())->first();
                    if ($slug) {
                        $slug->active = $isNowActive;
                        $slug->syncOriginalAttribute('active');
                    } else {
                        // The relation was loaded before the old slug even existed, unload it and let it lazy reload as needed
                        $this->unsetRelation('slugs');
                    }
                }
                if ($isNowActive) {
                    $this->disableLocaleSlugs($oldMatchingSlug->locale, $oldMatchingSlug->getKey());
                }
            }
        } else {
            $this->addOneSlug($slugParams, true);
        }
    }

    public function getExistingSlug(array $slugParams, bool $forRecreate = false): ?Model
    {
        unset($slugParams['active']);

        $query = $this->slugs();

        foreach ($slugParams as $key => $value) {
            //check variations of the slug
            if ($key === 'slug') {
                $query->where(function ($query) use ($value, $forRecreate) {
                    $query->orWhere('slug', $value);

                    if (!$forRecreate) {
                        $query->orWhere('slug', $value . '-' . $this->getSuffixSlug());
                        for ($i = 2; $i <= $this->nb_variation_slug; ++$i) {
                            $query->orWhere('slug', $value . '-' . $i);
                        }
                    }
                });
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    protected function addOneSlug(array $slugParams, $alreadySuffixed = false): void
    {
        if (!$alreadySuffixed) {
            $slugParams['slug'] = $this->suffixSlugIfExisting($slugParams);
        }

        $slugModel = \Illuminate\Database\Eloquent\Model::unguarded(fn() => $this->slugs()->create($slugParams));

        if ($this->relationLoaded('slugs')) {
            // Report update to slugs so that getSlug() returns the correct value
            $this->slugs->add($slugModel);
        }
        if (!$this->wasRecentlyCreated) {
            // There will not be any old slug if the model was just created
            $this->disableLocaleSlugs($slugParams['locale'], $slugModel->getKey());
        }
    }

    public function disableLocaleSlugs(string|array $locale = null, int|array $except_slug_id = 0): void
    {
        $query = $this->slugs()
            ->where('active', true)
            ->whereNotIn('id', Arr::wrap($except_slug_id));
        if ($locale !== null) {
            $query->whereIn('locale', Arr::wrap($locale));
        }
        $query->update(['active' => 0]);
        if ($this->relationLoaded('slugs')) {
            // Report update to slugs so that getSlug() returns the correct value after an update without needing a refresh
            $this->slugs->where('active', true)
                ->whereNotIn('id', Arr::wrap($except_slug_id))
                ->whereIn('locale', Arr::wrap($locale))
                ->each(function (Model $slug) {
                    $slug->active = false;
                    $slug->syncOriginalAttribute('active');
                });
        }
    }

    private function suffixSlugIfExisting(array $slugParams): string
    {
        $slugBackup = $slugParams['slug'];

        unset($slugParams['active']);


        for ($i = 2; $i <= $this->nb_variation_slug + 1; ++$i) {
            /** @var Builder $qCheck */
            $qCheck = $this->getSlugModelClass()::query();
            $qCheck->whereNull($this->getDeletedAtColumn());
            $qCheck->whereNot($this->getForeignKey(), $this->getKey());

            foreach ($slugParams as $key => $value) {
                $qCheck->where($key, '=', $value);
            }

            if ($qCheck->doesntExist()) {
                break;
            }

            if (!empty($slugParams['slug'])) {
                $slugParams['slug'] = $slugBackup . (($i > $this->nb_variation_slug) ? "-" . $this->getSuffixSlug() : "-{$i}");
            }
        }

        return $slugParams['slug'];
    }

    /**
     * @deprecated use suffixSlugIfExisting instead
     * Checks if a slug needs a suffix due to a conflict with another model.
     */
    private function slugNeedsSuffix(array $slugParams): bool
    {
        trigger_deprecation('area17/twill', '3.5', 'The slugNeedsSuffix method is deprecated and will be removed in 4.x use suffixSlugIfExisting instead');

        unset($slugParams['active']);

        $hasExisting = false;

        for ($i = 2; $i <= $this->nb_variation_slug + 1; $i++) {
            $qCheck = $this->getSlugModelClass()::query();
            $qCheck->whereNull($this->getDeletedAtColumn());
            foreach ($slugParams as $key => $value) {
                $qCheck->where($key, '=', $value);
            }

            if ($qCheck->doesntExist()) {
                break;
            }

            $hasExisting = true;
        }

        return $hasExisting;
    }

    /**
     * Returns the active slug object for this model.
     */
    public function getActiveSlug(?string $locale = null): ?Model
    {
        return $this->slugs->first(function ($slug) use ($locale) {
            return ($slug->locale === ($locale ?? app()->getLocale())) && $slug->active;
        });
    }

    /**
     * Returns the fallback active slug object for this model.
     */
    public function getFallbackActiveSlug(): ?Model
    {
        return $this->slugs->first(function ($slug) {
            return $slug->locale === config('translatable.fallback_locale') && $slug->active;
        });
    }

    /**
     * Returns the active slug string for this model.
     */
    public function getSlug(?string $locale = null): string
    {
        if (($slug = $this->getActiveSlug($locale)) !== null) {
            return $slug->slug;
        }

        if (config('translatable.use_property_fallback', false) && (($slug = $this->getFallbackActiveSlug()) != null)) {
            return $slug->slug;
        }

        return "";
    }

    public function getSlugAttribute(): string
    {
        return $this->getSlug();
    }

    public function getSlugDeps(): array
    {
        return $this->slugDeps ?? array_slice($this->slugAttributes ?? [], 1);
    }
    public function getSlugFields(): array
    {
        if (!isset($this->slugFields) && isset($this->slugAttributes)) {
            trigger_deprecation('area17/twill', '3.5', 'The slugAttributes property has been deprecated instead define slug fields with the slugFields property and additional columns with the slugDeps property');
        }
        return $this->slugFields ?? array_slice($this->slugAttributes ?? [], 0, 1);
    }

    public function getSlugParams(?string $locale = null, bool $skipUnchanged = false): ?array
    {
        $translatedAttributes = $this->getTranslatedAttributes();
        $slugParams = [];
        $deps = $this->getSlugDeps();
        $fields = $this->getSlugFields();
        if (empty($fields)) {
            return $locale === null ? [] : null;
        }
        foreach ($locale ? [$locale] : getLocales() as $appLocale) {
            if ($appLocale === $locale || $locale === null) {
                $wasChanged = $this->wasRecentlyCreated;

                $translation = method_exists($this, 'translate') ? $this->translate($appLocale, $this->usePropertyFallback()) : null;
                $getAttributeValue = function ($attribute) use ($translatedAttributes, $appLocale, $translation, &$wasChanged) {
                    if (in_array($attribute, $translatedAttributes)) {
                        if (!$wasChanged && $translation?->isDirty($attribute)) {
                            $wasChanged = true;
                        }
                        if (!isset($translation->$attribute) && config('translatable.use_property_fallback', false)) {
                            $fallback = $this->getFallbackLocale($appLocale);
                            $fallbackTranslation = $this->translate($fallback);
                            if (!$wasChanged && $fallbackTranslation?->isDirty($attribute)) {
                                $wasChanged = true;
                            }
                            return $fallbackTranslation?->$attribute;
                        }
                        return $translation?->$attribute;
                    }
                    if (!$wasChanged && $this->isDirty($attribute)) {
                        $wasChanged = true;
                    }
                    return $this->$attribute;
                };

                $slugFields = array_filter(array_map($getAttributeValue, $fields));

                if (empty($slugFields)) {
                    // Skip empty slugs
                    continue;
                }
                $slugDependencies = array_filter(array_combine($deps, array_map($getAttributeValue, $deps)));
                if (count($slugDependencies) !== count($deps)) {
                    $missing = array_keys(array_diff_key(array_flip($deps), $slugDependencies));
                    throw new \Exception('The slug dependencies ' . (Arr::join($missing, ', ', ' and ') . ' are missing'));
                }
                $slugParam = [
                        'active' => $translation?->active ?? true,
                        'slug' => Arr::join($slugFields, '-'),
                        'locale' => $appLocale,
                    ] + $slugDependencies;

                if ($locale != null) {
                    return !$skipUnchanged || $wasChanged ? $slugParam : null;
                }

                if (!$skipUnchanged || $wasChanged) {
                    $slugParams[$appLocale] = $slugParam;
                }
            }
        }

        return $locale === null ? $slugParams : null;
    }

    /** @deprecated */
    public function getSingleSlugParams(?string $locale = null): ?array
    {
        trigger_deprecation('area17/twill', '3.5', 'The getSingleSlugParams method is deprecated and will be removed in 4.x as it is not used, use getSlugParams instead');

        $slugParams = [];
        foreach (getLocales() as $appLocale) {
            if ($appLocale === $locale || $locale === null) {
                $attributes = $this->slugAttributes;
                if (!$attributes) {
                    continue;
                }
                $slugAttribute = array_shift($attributes);
                $slugDependenciesAttributes = [];
                foreach ($attributes as $attribute) {
                    if (!isset($this->$attribute)) {
                        throw new \Exception("You must define the field {$attribute} in your model");
                    }

                    $slugDependenciesAttributes[$attribute] = $this->$attribute;
                }

                if (!isset($this->$slugAttribute)) {
                    throw new \Exception("You must define the field {$slugAttribute} in your model");
                }

                $slugParam = [
                        'active' => 1,
                        'slug' => $this->$slugAttribute,
                        'locale' => $appLocale,
                    ] + $slugDependenciesAttributes;

                if ($locale !== null) {
                    return $slugParam;
                }

                $slugParams[] = $slugParam;
            }
        }

        return $locale === null ? $slugParams : null;
    }

    /**
     * Returns the database table name for this model's slugs.
     */
    public function getSlugsTable(): string
    {
        return $this->slugs()->getRelated()->getTable();
    }

    /**
     * Returns the database foreign key column name for this model.
     */
    public function getForeignKey(): string
    {
        return Str::snake(class_basename(get_class($this))) . "_id";
    }

    protected function getSuffixSlug(): string|int
    {
        return $this->getKey();
    }

    /**
     * Generate a URL friendly slug from a UTF-8 string.
     */
    public function getUtf8Slug(string $str, array $options = []): string
    {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

        $defaults = [
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => [],
            'transliterate' => true,
        ];

        // Merge options
        $options = array_merge($defaults, $options);

        $char_map = [
            // Latin
            'À' => 'A',
            'Á' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'AE',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Ï' => 'I',
            'Ð' => 'D',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ő' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ű' => 'U',
            'Ý' => 'Y',
            'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a',
            'á' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'ae',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'ï' => 'i',
            'ð' => 'd',
            'ñ' => 'n',
            'ò' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ő' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ű' => 'u',
            'ý' => 'y',
            'þ' => 'th',
            'ÿ' => 'y',

            // Latin symbols
            '©' => '(c)',

            // Greek
            'Α' => 'A',
            'Β' => 'B',
            'Γ' => 'G',
            'Δ' => 'D',
            'Ε' => 'E',
            'Ζ' => 'Z',
            'Η' => 'H',
            'Θ' => '8',
            'Ι' => 'I',
            'Κ' => 'K',
            'Λ' => 'L',
            'Μ' => 'M',
            'Ν' => 'N',
            'Ξ' => '3',
            'Ο' => 'O',
            'Π' => 'P',
            'Ρ' => 'R',
            'Σ' => 'S',
            'Τ' => 'T',
            'Υ' => 'Y',
            'Φ' => 'F',
            'Χ' => 'X',
            'Ψ' => 'PS',
            'Ω' => 'W',
            'Ά' => 'A',
            'Έ' => 'E',
            'Ί' => 'I',
            'Ό' => 'O',
            'Ύ' => 'Y',
            'Ή' => 'H',
            'Ώ' => 'W',
            'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a',
            'β' => 'b',
            'γ' => 'g',
            'δ' => 'd',
            'ε' => 'e',
            'ζ' => 'z',
            'η' => 'h',
            'θ' => '8',
            'ι' => 'i',
            'κ' => 'k',
            'λ' => 'l',
            'μ' => 'm',
            'ν' => 'n',
            'ξ' => '3',
            'ο' => 'o',
            'π' => 'p',
            'ρ' => 'r',
            'σ' => 's',
            'τ' => 't',
            'υ' => 'y',
            'φ' => 'f',
            'χ' => 'x',
            'ψ' => 'ps',
            'ω' => 'w',
            'ά' => 'a',
            'έ' => 'e',
            'ί' => 'i',
            'ό' => 'o',
            'ύ' => 'y',
            'ή' => 'h',
            'ώ' => 'w',
            'ς' => 's',
            'ϊ' => 'i',
            'ΰ' => 'y',
            'ϋ' => 'y',
            'ΐ' => 'i',

            // Turkish
            'Ş' => 'S',
            'İ' => 'I',
            'Ç' => 'C',
            'Ü' => 'U',
            'Ö' => 'O',
            'Ğ' => 'G',
            'ş' => 's',
            'ı' => 'i',
            'ç' => 'c',
            'ü' => 'u',
            'ö' => 'o',
            'ğ' => 'g',

            // Russian
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'Yo',
            'Ж' => 'Zh',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'J',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'C',
            'Ч' => 'Ch',
            'Ш' => 'Sh',
            'Щ' => 'Sh',
            'Ъ' => '',
            'Ы' => 'Y',
            'Ь' => '',
            'Э' => 'E',
            'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sh',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',

            // Ukrainian
            'Є' => 'Ye',
            'І' => 'I',
            'Ї' => 'Yi',
            'Ґ' => 'G',
            'є' => 'ye',
            'і' => 'i',
            'ї' => 'yi',
            'ґ' => 'g',

            // Kazakh
            'Ә' => 'A',
            'Ғ' => 'G',
            'Қ' => 'Q',
            'Ң' => 'N',
            'Ө' => 'O',
            'Ұ' => 'U',
            'ә' => 'a',
            'ғ' => 'g',
            'қ' => 'q',
            'ң' => 'n',
            'ө' => 'o',
            'ұ' => 'u',

            // Czech
            'Č' => 'C',
            'Ď' => 'D',
            'Ě' => 'E',
            'Ň' => 'N',
            'Ř' => 'R',
            'Ť' => 'T',
            'Ů' => 'U',
            'ď' => 'd',
            'ě' => 'e',
            'ň' => 'n',
            'ř' => 'r',
            'ť' => 't',
            'ů' => 'u',

            // Polish
            'Ą' => 'A',
            'Ć' => 'C',
            'Ę' => 'e',
            'Ł' => 'L',
            'Ń' => 'N',
            'Ó' => 'o',
            'Ś' => 'S',
            'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a',
            'ć' => 'c',
            'ę' => 'e',
            'ł' => 'l',
            'ń' => 'n',
            'ó' => 'o',
            'ś' => 's',
            'ź' => 'z',
            'ż' => 'z',

            // Latvian
            'Ā' => 'A',
            'Č' => 'C',
            'Ē' => 'E',
            'Ģ' => 'G',
            'Ī' => 'i',
            'Ķ' => 'k',
            'Ļ' => 'L',
            'Ņ' => 'N',
            'Š' => 'S',
            'Ū' => 'u',
            'Ž' => 'Z',
            'ā' => 'a',
            'č' => 'c',
            'ē' => 'e',
            'ģ' => 'g',
            'ī' => 'i',
            'ķ' => 'k',
            'ļ' => 'l',
            'ņ' => 'n',
            'š' => 's',
            'ū' => 'u',
            'ž' => 'z',

            // Romanian
            'Ă' => 'A',
            'Â' => 'A',
            'Î' => 'I',
            'Ș' => 'S',
            'Ț' => 'T',
            'ă' => 'a',
            'â' => 'a',
            'î' => 'i',
            'ș' => 's',
            'ț' => 't',
        ];

        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }

        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('#[^\p{L}\p{Nd}]+#u', $options['delimiter'], $str);

        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ?: mb_strlen($str, 'UTF-8')), 'UTF-8');

        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }

    /**
     * Generate a URL friendly slug from a given string.
     */
    public function urlSlugShorter(string $string): string
    {
        return strtolower(trim(
            preg_replace(
                '#[^0-9a-z]+#i',
                '-',
                html_entity_decode(
                    preg_replace(
                        '#&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);#i',
                        '$1',
                        htmlentities($string, ENT_QUOTES, 'UTF-8')
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                )
            ),
            '-'
        ));
    }

    /**
     * Returns the fully qualified namespace for this model.
     */
    public function getNamespace(): string
    {
        $pos = mb_strrpos(self::class, '\\');

        if ($pos === false) {
            return self::class;
        }

        return Str::substr(self::class, 0, $pos);
    }
}
