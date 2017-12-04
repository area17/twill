<?php

namespace A17\CmsToolkit\Http\Controllers\Front;

trait ShowWithPreview
{
    public function show($slug)
    {
        if (!isset($this->moduleName) || !isset($this->repository)) {
            throw new \Exception("You should at least provide a module name and inject a repository.");
        }

        if (!isset($this->routeName)) {
            $this->routeName = str_singular($this->moduleName);
        }

        if (!isset($this->showViewName)) {
            $this->showViewName = 'site.' . str_singular($this->moduleName);
        }

        if ($preview = request()->route()->getName() === $this->routeName . '.preview') {
            $item = $this->repository->forSlugPreview($slug);

            $previewSessionKey = '_preview_' . $this->moduleName . '_' . $item->id;
            if (session($previewSessionKey, false)) {
                $item = session($previewSessionKey);
            }

            $compareSessionKey = '_compare_' . $this->moduleName . '_' . $item->id;
            if (session($compareSessionKey, false)) {
                $itemToCompare = session($compareSessionKey);
            }
        }

        abort_unless($item = ($item ?? $this->repository->forSlug($slug, $this->showWith ?? [], $this->showWithCount ?? [], $this->showScopes ?? [])), 404, ucfirst($this->moduleName) . ' not found');

        if ($item->redirect) {
            return redirect()->to(route($this->routeName . '.show', $item->getSlug()));
        }

        return view($this->showViewName, [
            'item' => $item,
            'preview' => $preview,
            'compareHtml' => isset($itemToCompare) ? view($this->showViewName, [
                'item' => $itemToCompare,
                'preview' => false,
            ] + $this->showData($slug, $item))->render() : null,
            'previewHtml' => $preview ? view($this->showViewName, [
                'item' => $item,
                'preview' => false,
            ] + $this->showData($slug, $item))->render() : null,
        ] + $this->showData($slug, $item));
    }

    public function showData($slug, $item)
    {
        return [];
    }
}
