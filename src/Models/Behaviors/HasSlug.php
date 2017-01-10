<?php

namespace A17\CmsToolkit\Models\Behaviors;

use DB;

trait HasSlug
{
    private $nb_variation_slug = 3;

    protected static function bootHasSlug()
    {
        static::created(function ($model) {
            $model->setSlugs();
        });

        static::updated(function ($model) {
            $model->setSlugs();
        });
    }

    public function slugs()
    {
        return $this->hasMany("App\Models\Slugs\\" . $this->getSlugClassName());
    }

    public function getSlugClass()
    {
        $slugClassName = "App\Models\Slugs\\" . $this->getSlugClassName();
        return new $slugClassName;
    }

    protected function getSlugClassName()
    {
        return class_basename($this) . "Slug";
    }

    public function setSlugs()
    {
        foreach ($this->getSlugParams() as $slugParams) {
            unset($slugParams['active']);
            $this->updateOrNewSlug($slugParams);
        }
    }

    public function updateOrNewSlug($slugParams)
    {
        $slugParams['slug'] = str_slug($slugParams['slug']);

        //active old slug if already existing or create a new one
        if (($oldSlug = $this->getExistingSlug($slugParams)) != null) {
            if (!$oldSlug->active) {
                DB::table($this->getSlugsTable())->where('id', $oldSlug->id)->update(['active' => 1]);
                $this->disableLocaleSlugs($oldSlug->locale, $oldSlug->id);
            }
        } else {
            $this->addOneSlug($slugParams);
        }
    }

    public function getExistingSlug($slugParams)
    {
        $query = DB::table($this->getSlugsTable())->where($this->getForeignKey(), $this->id);

        foreach ($slugParams as $key => $value) {
            //check variations of the slug
            if ($key == 'slug') {
                $query->where(function ($query) use ($value) {
                    $query->orWhere('slug', $value);
                    $query->orWhere('slug', $value . '-' . $this->getSuffixSlug());
                    for ($i = 1; $i < $this->nb_variation_slug; $i++) {
                        $query->orWhere('slug', $value . '-' . $i);
                    }
                });
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    protected function addOneSlug($slugParams)
    {
        $datas = [];
        foreach ($slugParams as $key => $value) {
            $datas[$key] = $value;
        }

        $datas['slug'] = $this->suffixSlugIfExisting($slugParams);
        $datas['active'] = $slugParams['active'] ?? 1;
        $datas[$this->getForeignKey()] = $this->id;

        $id = DB::table($this->getSlugsTable())->insertGetId($datas);

        $this->disableLocaleSlugs($slugParams['locale'], $id);
    }

    public function disableLocaleSlugs($locale, $except_slug_id = 0)
    {
        DB::table($this->getSlugsTable())
            ->where($this->getForeignKey(), $this->id)
            ->where('id', '<>', $except_slug_id)
            ->where('locale', $locale)
            ->update(['active' => 0])
        ;
    }

    private function suffixSlugIfExisting($slugParams)
    {
        $slugBackup = $slugParams['slug'];
        $table = $this->getSlugsTable();
        for ($i = 1; $i <= $this->nb_variation_slug; $i++) {
            $qCheck = DB::table($table);

            foreach ($slugParams as $key => $value) {
                $qCheck->where($key, '=', $value);
            }

            if ($qCheck->first() == null) {
                break;
            }

            if (!empty($slugParams['slug'])) {
                $slugParams['slug'] = $slugBackup . (($i == $this->nb_variation_slug) ? "-" . $this->getSuffixSlug() : "-{$i}");
            }
        }

        return $slugParams['slug'];
    }

    public function getActiveSlug($locale = null)
    {
        return $this->slugs->first(function ($slug) use ($locale) {
            return ($slug->locale === ($locale ?? app()->getLocale())) && $slug->active;
        }) ?? null;
    }

    public function getSlug($locale = null)
    {
        if (($slug = $this->getActiveSlug($locale)) != null) {
            return $slug->slug;
        }

        return "";
    }

    public function getSlugParams($locale = null)
    {
        $slugParams = [];
        foreach ($this->translations as $translation) {
            if ($translation->locale == $locale || $locale == null) {
                $attributes = $this->slugAttributes;

                $slugAttribute = array_shift($attributes);

                $slugDependenciesAttributes = [];
                foreach ($attributes as $attribute) {
                    $slugDependenciesAttributes[$attribute] = $this->$attribute;
                }

                $slugParam = [
                    'active' => $translation->active,
                    'slug' => $translation->$slugAttribute,
                    'locale' => $translation->locale,
                ] + $slugDependenciesAttributes;

                if ($locale != null) {
                    return $slugParam;
                }

                $slugParams[] = $slugParam;
            }
        }

        return $locale == null ? $slugParams : null;
    }

    public function getSlugsTable()
    {
        return $this->slugs()->getRelated()->getTable();
    }

    public function getForeignKey()
    {
        return snake_case(class_basename(get_class($this))) . "_id";
    }

    protected function getSuffixSlug()
    {
        return $this->id;
    }
}
