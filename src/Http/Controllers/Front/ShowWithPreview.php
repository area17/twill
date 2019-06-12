<?php

namespace A17\Twill\Http\Controllers\Front;

trait ShowWithPreview
{
    /**
     * @param string $slug
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Exception
     */
    public function show($slug)
    {
        if (!isset($this->moduleName) || !isset($this->repository)) {
            throw new \Exception("You should at least provide a module name and inject a repository.");
        }

        if (!isset($this->routeName)) {
            $this->routeName = $this->moduleName;
        }

        if (!isset($this->showViewName)) {
            $this->showViewName = config('twill.frontend.views_path', 'site') . '.' . str_singular($this->moduleName);
        }

        if (ends_with(request()->route()->getName(), $this->routeName . '.preview')) {
            $item = $this->getItemPreview($slug);
        }

        abort_unless($item = ($item ?? $this->getItem($slug)), 404, ucfirst($this->moduleName) . ' not found');

        if ($item->redirect) {
            return redirect()->to(route($this->routeName . '.show', $item->getSlug()));
        }

        return view($this->showViewName, [
            'item' => $item,
        ] + $this->showData($slug, $item));
    }

    /**
     * @param string $slug
     * @return \A17\Twill\Models\Model|null
     */
    protected function getItem($slug)
    {
        return $this->repository->forSlug(
            $slug,
            $this->showWith ?? [],
            $this->showWithCount ?? [],
            $this->showScopes ?? []
        );
    }

    /**
     * @param $slug
     * @return \A17\Twill\Models\Model|null
     */
    protected function getItemPreview($slug)
    {
        return $this->repository->forSlugPreview(
            $slug,
            $this->showWith ?? [],
            $this->showWithCount ?? []
        );
    }

    /**
     * @param string $slug
     * @param mixed $item
     * @return array
     */
    protected function showData($slug, $item)
    {
        return [];
    }
}
