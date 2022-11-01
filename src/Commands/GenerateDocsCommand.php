<?php

namespace A17\Twill\Commands;

use A17\Docs\BladeComponentElement;
use A17\Docs\PhpTorchExtension;
use A17\Docs\RelativeLinksExtension;
use A17\Docs\BladeComponentRenderer;
use A17\Docs\BladeComponentStart;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Renderer\Block\ListBlockRenderer;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsGenerator;
use League\CommonMark\Extension\TableOfContents\TableOfContentsRenderer;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Query;
use League\CommonMark\Renderer\HtmlRenderer;

class GenerateDocsCommand extends Command
{
    protected $signature = 'twill:staticdocs:generate {--updatesOnly=true} {--fresh}';

    protected $description = 'Generate the static documentation';

    private MarkdownConverter $converter;

    public function handle()
    {
        config()->set('torchlight.token', env('TORCHLIGHT_API_TOKEN'));
        config()->set('torchlight.theme', 'nord');

        $environment = new Environment([
            'heading_permalink' => [
                'symbol' => '# ',
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new PhpTorchExtension());
        $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addBlockStartParser(new BladeComponentStart())->addRenderer(
            BladeComponentElement::class,
            new BladeComponentRenderer()
        );
        $environment->addRenderer(Link::class, new RelativeLinksExtension());

        $this->converter = new MarkdownConverter($environment);

        // Register blade.
        View::addNamespace('twilldocs', __DIR__ . '/../../docs/_templates');
        Blade::componentNamespace('twilldocs.components', 'twilldocs');

        $dir = __DIR__ . '/../../docs/';

        $disk = Storage::build([
            'driver' => 'local',
            'root' => $dir,
        ]);

        if ($this->option('fresh')) {
            $disk->deleteDirectory('_build');
        }

        $contents = [];

        $navTree = [];

        if ($disk->exists('_build/updated.json') && $last = json_decode($disk->get('_build/updated.json'), true)) {
            $navTree = $last;
        }

        $hasChange = false;

        $unsorted = $disk->allFiles('content');

        // This sort needs to be reworked.
        $sorted = Arr::sort($unsorted, function (string $item) {
            $parts = explode('/', Str::after($item, '/'));

            $index = '';

            foreach ($parts as $part) {
                if (Str::contains($part, '_')) {
                    $index .= (int)Str::before($part, '_');
                } else {
                    $index .= 99;
                }
            }

            return $index;
        });

        // Process docs.
        foreach ($sorted as $relativePath) {
            $url = $this->withoutNumbers(Str::replace(['content/', '.md'], ['/', '.html'], $relativePath));

            if (isset($navTree['last_updated']) && $disk->lastModified($relativePath) <= $navTree['last_updated']) {
                if ($this->option('updatesOnly') === false) {
                    $this->line('Skipped (no update) ' . $relativePath);
                }
                continue;
            }

            if (Str::endsWith($relativePath, '.md')) {
                $this->line('Processing ' . $relativePath . ' -> ' . $url);
                $title = Str::title(
                    Str::replace('-', ' ', Str::before(Str::after(Str::afterLast($relativePath, '/'), '_'), '.md'))
                );

                $document = $this->converter->convert($disk->get($relativePath));

                $titleNode = (new Query())->where(Query::type(Heading::class))->findOne(
                    $document->getDocument()->firstChild()
                )?->firstChild();

                if ($titleNode) {
                    if ($titleNode instanceof HeadingPermalink) {
                        $title = $titleNode->next()->getLiteral();
                    } else {
                        $title = $titleNode->getLiteral();
                    }
                }

                // Parse TOC.
                $TOCGenerator = new TableOfContentsGenerator('bullet', 'relative', 2, 3, 'content');
                $toc = $TOCGenerator->generate($document->getDocument());
                $tocRendered = null;

                if ($toc) {
                    $TOCRenderer = new TableOfContentsRenderer(new ListBlockRenderer());
                    $tocRendered = (string)$TOCRenderer->render($toc, new HtmlRenderer($environment));
                }

                $structure = explode('/', $this->withoutNumbers(Str::beforeLast($relativePath, '/')));

                // Remove the first item as that is the content dir.
                unset($structure[0]);

                $treeData = [
                    'title' => $title,
                    'url' => $url,
                    'relativePath' => $this->withoutNumbers($relativePath),
                    'content' => (string)$document,
                    'toc' => $tocRendered,
                ];

                if (Str::contains($relativePath, 'index.md')) {
                    foreach ($treeData as $key => $value) {
                        Arr::set(
                            $navTree,
                            implode('.items.', $structure) . '.' . $key,
                            $value,
                        );
                    }
                } else {
                    Arr::set(
                        $navTree,
                        implode('.items.', $structure) . '.items.' . Str::replace(
                            '.md',
                            '',
                            $this->withoutNumbers($relativePath)
                        ),
                        $treeData
                    );
                }
            }

            $hasChange = true;
        }

        // Copy the files if any markdown changed.
        if ($hasChange) {
            foreach ($sorted as $relativePath) {
                if (!Str::endsWith($relativePath, '.md')) {
                    $disk->copy(
                        $relativePath,
                        '_build/' . $this->withoutNumbers(Str::replaceFirst('content/', '', $relativePath))
                    );
                }
            }
        }

        // Check for changes in the _templates folder.
        if (!$hasChange) {
            foreach ($disk->allFiles('_templates') as $templatesPath) {
                if (
                    isset($navTree['last_updated']) &&
                    $disk->lastModified($templatesPath) >= $navTree['last_updated']
                ) {
                    if (!$hasChange) {
                        $this->line('Template changed, rebuilding.');
                        $hasChange = true;
                    }
                }
                if (!Str::endsWith($templatesPath, ['.css', '.php'])) {
                    $disk->copy($templatesPath, '_build/' . Str::replaceFirst('_templates/', '', $templatesPath));
                }
            }
        }

        if ($hasChange) {
            $disk->put('_build/updated.json', json_encode(array_merge($navTree, ['last_updated' => time()])));

            // Prepare data for the renderer.
            $tree = $navTree;
            unset($tree['last_updated']);

            $layout = $disk->get('_templates/layout.blade.php');

            $this->handleTree($tree, $navTree, $layout, $disk, $contents);

            // Finally process the styles.
            $tailwindFrom = realpath($dir . '_templates/style.css');
            $tailwindTo = realpath($dir . '_build');

            shell_exec(
                'cd ' . realpath($dir) . ' && tailwindcss -i ' . $tailwindFrom . ' -o ' . $tailwindTo . '/style.css'
            );
        }
    }

    private function handleTree(array $tree, array $navTree, $layout, $disk, $contents): void
    {
        foreach ($tree as $treeItem) {
            $data = $treeItem;
            $data['tree'] = $navTree;
            unset($data['tree']['last_updated']);

            $content = Blade::render(
                $layout,
                $data,
            );

            $disk->put(
                '_build/' . Str::replace('.md', '.html', Str::replaceFirst('content/', '', $data['relativePath'])),
                $content
            );

            $this->handleTree($treeItem['items'] ?? [], $navTree, $layout, $disk, $contents);
        }
    }

    private function withoutNumbers(string $string): string
    {
        return preg_replace('/(\d*_)/', '', $string);
    }
}
