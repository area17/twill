<?php

namespace A17\Twill\Tests\Integration;

use App\Http\Controllers\Twill\NodeController;
use App\Models\Node;
use App\Repositories\NodeRepository;

class NestedModuleTest extends NestedModuleTestBase
{
    public function testReorderNestedModuleItems(): void
    {
        // Given some Node items
        $parents = $this->createNodes(['One', 'Two', 'Three']);
        $children = $this->createNodes(['A', 'B', 'C']);
        $this->assertEquals(6, Node::count());
        $this->assertEquals(6, Node::where('parent_id', null)->count());

        // When they are arranged in a parent-child relationship
        $data = $this->arrangeNodes($parents, $children);
        $this->httpRequestAssert('/twill/nodes/reorder', 'POST', ['ids' => $data]);

        // Then the correct structure is reflected in the DB
        $this->assertEquals(3, $parents[0]->refresh()->children()->count());
        $this->assertEquals(0, $parents[1]->refresh()->children()->count());
        $this->assertEquals(0, $parents[2]->refresh()->children()->count());
    }

    public function testNestedModuleBrowseParents(): void
    {
        NodeController::$forceShowOnlyParentItemsInBrowsers = true;

        // Given some Node items arranged in a parent-child relationship
        $parents = $this->createNodes(['One', 'Two', 'Three']);
        $children = $this->createNodes(['A', 'B', 'C']);
        $data = $this->arrangeNodes($parents, $children);
        $this->httpRequestAssert('/twill/nodes/reorder', 'POST', ['ids' => $data]);

        // When queried through the `browser` endpoint
        $this->httpRequestAssert('/twill/nodes/browser', 'GET', []);
        $this->assertJson($this->content());
        $result = json_decode($this->content(), true);

        // Then only parents are returned
        $this->assertCount(3, $result['data']);
    }

    public function testNestedModuleBrowseParentsAndChildren(): void
    {
        NodeController::$forceShowOnlyParentItemsInBrowsers = false;

        // Given some Node items arranged in a parent-child relationship
        $parents = $this->createNodes(['One', 'Two', 'Three']);
        $children = $this->createNodes(['A', 'B', 'C']);
        $data = $this->arrangeNodes($parents, $children);
        $this->httpRequestAssert('/twill/nodes/reorder', 'POST', ['ids' => $data]);

        // When queried through the `browser` endpoint
        $this->httpRequestAssert('/twill/nodes/browser', 'GET', []);
        $this->assertJson($this->content());
        $result = json_decode($this->content(), true);

        // Then all items are returned
        $this->assertCount(6, $result['data']);
    }

    public function testAncestorsSlugCreationOrder(): void
    {
        $repository = app(NodeRepository::class);
        $childlvl2 = $repository->create(
            [
                'title' => 'child level 2',
                'published' => true,
                'position' => 2,
            ]
        );

        $childlvl1 = $repository->create(
            [
                'title' => 'child level 1',
                'published' => true,
                'position' => 3,
            ]
        );

        $childlvl0 = $repository->create(
            [
                'title' => 'parent',
                'published' => true,
                'position' => 4,
            ]
        );

        $data = [
            [
                'id' => $childlvl0->id,
                'children' => [
                    [
                        'id' => $childlvl1->id,
                        'children' => [
                            [
                                'id' => $childlvl2->id,
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->postJson('/twill/nodes/reorder', ['ids' => $data])->assertOk();

        $this->assertEquals(
            'parent/child-level-1/child-level-2',
            $childlvl2->refresh()->ancestorsSlug . '/' . $childlvl2->getSlug()
        );
    }
}
