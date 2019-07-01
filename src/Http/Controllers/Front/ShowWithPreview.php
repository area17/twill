<?php

namespace A17\Twill\Http\Controllers\Front;

use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;

trait ShowWithPreview
{
    /**
     * @param string $slug
     * @param Redirector $redirector
     * @param ViewFactory $viewFactory
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function show(string $slug, Redirector $redirector, ViewFactory $viewFactory)
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

        if (Str::endsWith(request()->route()->getName(), $this->routeName . '.preview')) {
            $item = $this->getItemPreview($slug);
        }

        abort_unless($item = ($item ?? $this->getItem($slug)), 404, ucfirst($this->moduleName) . ' not found');

        if ($item->redirect) {
            return $redirector->to(route($this->routeName . '.show', $item->getSlug()));
        }

        return $viewFactory->make($this->showViewName, [
            'item' => $item,
        ] + $this->showData($slug, $item));
    }

    protected function getItem($slug)
    {
        return $this->repository->forSlug(
            $slug,
            $this->showWith ?? [],
            $this->showWithCount ?? [],
            $this->showScopes ?? []
        );
    }

    protected function getItemPreview($slug)
    {
        return $this->repository->forSlugPreview(
            $slug,
            $this->showWith ?? [],
            $this->showWithCount ?? []
        );
    }

    protected function showData($slug, $item)
    {
        return [];
    }
}
