<?php

namespace A17\Docs;

use Illuminate\Support\Str;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\LinkRenderer;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

class RelativeLinksExtension implements NodeRendererInterface, ConfigurationAwareInterface
{
    private ConfigurationInterface $config;

    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        $rendered = new LinkRenderer();
        $rendered->setConfiguration($this->config);

        $node->setUrl(Str::replace('.md', '.html', preg_replace('/(\d*_)/', '', $node->getUrl())));

        return $rendered->render($node, $childRenderer);
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }
}
