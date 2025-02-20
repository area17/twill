<?php

namespace A17\Twill\Http\Controllers\Front;

use Illuminate\Config\Repository as Config;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ShowWithPreview
{
    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function show(
        string $slug,
        Request $request,
        Redirector $redirector,
        ViewFactory $viewFactory,
        Config $config
    ) {
        if (!isset($this->moduleName) || !isset($this->repository)) {
            throw new \Exception("You should at least provide a module name and inject a repository.");
        }

        if (!isset($this->routeName)) {
            $this->routeName = $this->moduleName;
        }

        if (!isset($this->showViewName)) {
            $this->showViewName = $config->get('twill.frontend.views_path', 'site') . '.' . Str::singular($this->moduleName);
        }

        if (Str::endsWith($request->route()->getName(), $this->routeName . '.preview')) {
            $item = $this->getItemPreview($slug);
        }

        $item = ($item ?? $this->getItem($slug));

        if (!$item) {
            throw new NotFoundHttpException(ucfirst($this->moduleName) . ' not found');
        }

        if ($item->redirect) {
            return $redirector->to(route($this->routeName . '.show', $item->getSlug()));
        }

        return $viewFactory->make($this->showViewName, [
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
