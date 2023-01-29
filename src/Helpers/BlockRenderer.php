<?php

namespace A17\Twill\Helpers;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Models\Block as A17Block;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\Media;
use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Blocks\RenderData;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

/**
 * This class can take either an Editor or a Cms array of data to render.
 *
 * Based on the input it will build a rendering array (nested) based on the block classes.
 *
 * A block class list may look like this:
 *
 * ```
 * TwoColumnsBlock
 *  ->children: [
 *      TextBlock: [
 *          ActionBlock
 *      ]
 *      ImageBlock: [
 *      ]
 *  ]
 * ```
 */
class BlockRenderer
{
    /**
     * A list of root blocks.
     *
     * @var \A17\Twill\Services\Blocks\Block[]
     */
    public array $rootBlocks = [];

    public bool $inEditor = false;

    public function __construct(array $blocks = [], bool $inEditor = false)
    {
        $this->rootBlocks = $blocks;
        $this->inEditor = $inEditor;
    }

    public function render(
        array $blockViewMappings = [],
        array $data = [],
    ): string {
        $viewResult = [];
        /** @var Block $block */
        foreach ($this->rootBlocks as $block) {
            if ($block->componentClass) {
                $component = $block->componentClass::forRendering(
                    $block->renderData->block,
                    $block->renderData,
                    $this->inEditor
                );

                $viewResult[] = $component->render()->with(array_merge($data, $component->data()));
            } else {
                $viewResult[] = $block->renderView($blockViewMappings, $data, $this->inEditor);
            }
        }

        return implode('', $viewResult);
    }

    public static function fromCmsArray(array $data): self
    {
        return new self(
            [self::getNestedBlocksForData($data, $data['editor_name'])],
            true
        );
    }

    private static function getNestedBlocksForData(
        array $data,
        string $editorName,
        string $parentEditorName = null
    ): Block {
        $class = clone Block::getForComponent($data['type'], $data['is_repeater'] ?? false)->newInstance();

        if (! $class) {
            $type = Str::replace('a17-block-', '', $data['type']);
            // It is important to always clone this as it would otherwise overwrite the renderData inside.
            $class = clone Block::getForType($type, $data['is_repeater'] ?? false)->newInstance();
        }

        $type = $class->name;

        $children = [];

        foreach ($data['blocks'] as $editor => $childBlocks) {
            foreach ($childBlocks as $childBlock) {
                $children[] = self::getNestedBlocksForData(
                    $childBlock,
                    editorName: $editorName,
                    parentEditorName: $editor
                );
            }
        }

        // Load the original block if it exists or make a new one then fill it with the data from the request.
        $block = twillModel('block')::findOrNew($data['id'] ?? null);
        $block->fill(
            [
                'type' => $type,
                'content' => $data['content'],
                'editor_name' => $editorName,
            ]
        );

        $block->setRelation('children', self::getChildren($children));

        $block->medias = self::getMedias($data);

        $class->setRenderData(
            new RenderData(
                block: $block,
                editorName: $editorName,
                children: $children,
                parentEditorName: $parentEditorName,
                inEditor: true,
            )
        );

        return $class;
    }

    /**
     * Generates a simple pivot object that tricks laravel into believing
     * it is actual pivot data further along the rendering pipeline.
     */
    private static function getPivotDummy(array $data): object
    {
        return new class ($data) implements Arrayable {
            public function __construct(public array $data)
            {
            }

            public function __get(string $name): mixed
            {
                return $this->data[$name] ?? null;
            }

            public function toArray(): array
            {
                return $this->data;
            }
        };
    }

    private static function getChildren(array $blocks): Collection
    {
        if ($blocks === []) {
            return new Collection();
        }

        $blocksCollection = Collection::make();

        /** @var Block $block */
        foreach ($blocks as $block) {
            $blocksCollection->push($block->renderData?->block);
        }

        return $blocksCollection;
    }

    /**
     * Heavily modified version of that in HandleMedias.
     *
     * This basically generates a dummy relation that can be injected into an existing or new Block model.
     *
     * This helps to render blocks on the fly without the need of having a saved crop config in the mediables table.
     */
    private static function getMedias(array $fields): Collection
    {
        $medias = Collection::make();

        if (isset($fields['medias'])) {
            foreach ($fields['medias'] as $role => $mediasForRole) {
                if (config('twill.media_library.translated_form_fields', false) && Str::contains($role, ['[', ']'])) {
                    $start = strpos($role, '[') + 1;
                    $finish = strpos($role, ']', $start);
                    $locale = substr($role, $start, $finish - $start);
                    $role = strtok($role, '[');
                }

                $locale = $locale ?? config('app.locale');

                $crops = TwillBlocks::getAllCropConfigs();

                if (array_key_exists($role, $crops)) {
                    Collection::make($mediasForRole)->each(function ($media) use (&$medias, $role, $locale) {
                        $customMetadatas = $media['metadatas']['custom'] ?? [];
                        if (isset($media['crops']) && ! empty($media['crops'])) {
                            foreach ($media['crops'] as $cropName => $cropData) {
                                $media = (new Media())->forceFill(
                                    $data = [
                                        'id' => $media['id'],
                                        'uuid' => Media::find($media['id'])->uuid ?? null,
                                        'crop' => $cropName,
                                        'role' => $role,
                                        'locale' => $locale,
                                        'ratio' => $cropData['name'],
                                        'crop_w' => $cropData['width'],
                                        'crop_h' => $cropData['height'],
                                        'crop_x' => $cropData['x'],
                                        'crop_y' => $cropData['y'],
                                        'metadatas' => json_encode($customMetadatas),
                                    ]
                                );
                                $media->setRelation('pivot', self::getPivotDummy($data));

                                $medias->push($media);
                            }
                        }
                    });
                }
            }
        }

        return $medias;
    }

    public static function fromEditor(
        TwillModelContract $model,
        string $editorName,
    ): self {
        if (! isset(class_uses_recursive($model)[HasBlocks::class])) {
            throw new Exception('Model ' . $model::class . ' does not implement HasBlocks');
        }

        $renderer = new self();

        /** @var \A17\Twill\Models\Block[] $blocks */
        $blocks = $model->blocks->where('editor_name', $editorName)->where('parent_id', null);

        foreach ($blocks as $block) {
            $data = self::getNestedBlocksForBlock($block, $model, $editorName);
            $renderer->rootBlocks[] = $data;
        }

        return $renderer;
    }

    public static function getNestedBlocksForBlock(
        A17Block $block,
        TwillModelContract $rootModel,
        string $editorName
    ): Block {
        // We do not know if the block is a repeater or block so we use the first match.
        $class = Block::findFirstWithType($block->type)->newInstance();

        $children = [];

        foreach ($block->children ?? [] as $childBlock) {
            $children[] = self::getNestedBlocksForBlock(
                block: $childBlock,
                rootModel: $rootModel,
                editorName: $editorName,
            );
        }

        $class->setRenderData(
            new RenderData(
                block: $block,
                editorName: $editorName,
                children: $children,
                model: $rootModel,
                parentEditorName: $block->child_key
            )
        );

        return $class;
    }
}
