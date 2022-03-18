<?php

namespace A17\Twill\Tests\Integration;

use App\Http\Controllers\Twill\NodeController;
use App\Models\Node;
use App\Repositories\NodeRepository;

class NestedModuleTest extends TestCase
{
    public $example = 'tests-nestedmodules';

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function createNodes($titles)
    {
        return collect($titles)->map(function ($name) {
            return app(NodeRepository::class)->create([
                'title' => $name,
                'published' => true,
            ]);
        });
    }

    public function arrangeNodes($parents, $children)
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

    // FIXME — this is needed for the new admin routes to take effect in the next test,
    // because files are copied in `setUp()` after the app is initialized.
    public function testDummy()
    {
        $this->assertTrue(true);
    }

    public function testReorderNestedModuleItems()
    {
        // Given some Node items
        $parents = $this->createNodes(['One', 'Two', 'Three']);
        $children = $this->createNodes(['A', 'B', 'C']);
        $this->assertEquals(6, Node::count());
        $this->assertEquals(0, Node::where(['parent_id', null])->count());

        // When they are arranged in a parent-child relationship
        $data = $this->arrangeNodes($parents, $children);
        $this->httpRequestAssert('/twill/nodes/reorder', 'POST', ['ids' => $data]);

        // Then the correct structure is reflected in the DB
        $this->assertEquals(3, $parents[0]->refresh()->children()->count());
        $this->assertEquals(0, $parents[1]->refresh()->children()->count());
        $this->assertEquals(0, $parents[2]->refresh()->children()->count());
    }

    public function testNestedModuleBrowseParents()
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
        $this->assertEquals(3, count($result['data']));
    }

    public function testNestedModuleBrowseParentsAndChildren()
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
        $this->assertEquals(6, count($result['data']));
    }
}
