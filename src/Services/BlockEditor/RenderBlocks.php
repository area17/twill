<?php

namespace A17\CmsToolkit\BlockEditor;

class RenderBlocks
{
    public function fromFullJsonToHtmlAndMetadatas($blocksJson, $options = [])
    {

        $input = json_decode($blocksJson, true);
        $html = null;
        $blocks_metadatas = [];

        if (is_array($input)) {
            foreach ($input['data'] as $block) {
                $renderer = $this->getRenderer($block, $options);
                if ($renderer) {
                    $html .= $renderer->renderToHtml();
                    $blocks_metadatas += $renderer->renderMetadatas();
                }
            }
        }

        return ['html' => html_entity_decode($html), 'metadatas' => $blocks_metadatas];
    }

    public function fromFullJsonToHtml($blocksJson, $options = [])
    {

        $input = json_decode($blocksJson, true);
        $html = null;

        if (is_array($input)) {
            foreach ($input['data'] as $block) {
                $renderer = $this->getRenderer($block, $options);
                if ($renderer) {
                    $html .= $renderer->renderToHtml();
                }
            }
        }

        return html_entity_decode($html);
    }

    public function fromSingleJsonToHtml($blockJson, $options = [])
    {

        $block = json_decode($blockJson, true);
        $html = null;

        $renderer = $this->getRenderer($block, $options);

        if ($renderer) {
            $html .= $renderer->renderToHtml();
        }

        return html_entity_decode($html);
    }

    private function getRenderer($block, $options = [])
    {
        if (!isset($block['data']) || !array_key_exists($block['type'], config('cms-toolkit.block_editor.blocks'))) {
            return null;
        }

        $class = config('cms-toolkit.block_editor.blocks')[$block['type']];

        return new $class($block, $options);
    }

    public function listImagesForSitemap($blocksJson)
    {
        $input = json_decode($blocksJson, true);
        $images = collect();

        if (is_array($input)) {

            foreach ($input['data'] as $block) {

                if (!isset($block['data']) || !array_key_exists($block['type'], config('cms-toolkit.block_editor.blocks'))) {
                    continue;
                }

                foreach (config('cms-toolkit.block_editor.sitemap_blocks') as $blockClass) {
                    $block = new $blockClass($block);

                    $images = $images->merge($block->listImagesForSitemap());
                }
            }
        }

        return $images;
    }
}
