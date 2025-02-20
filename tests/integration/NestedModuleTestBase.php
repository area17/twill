<?php

namespace A17\Twill\Tests\Integration;

use App\Repositories\NodeRepository;
use Illuminate\Support\Collection;

class NestedModuleTestBase extends TestCase
{
    public ?string $example = 'tests-nestedmodules';

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function createNodes($titles): Collection
    {
        return collect($titles)->map(function ($name) {
            return app(NodeRepository::class)->create([
                'title' => $name,
                'published' => true,
            ]);
        });
    }

    public function arrangeNodes($parents, $children): array
    {
        $data = $parents->map(function ($item) {
            return ['id' => $item->id, 'children' => []];
        })->all();

        // All children are attached to the first parent
        $data[0]['children'] = $children->map(function ($item) {
            return ['id' => $item->id, 'children' => []];
        })->all();

        return $data;
    }
}
