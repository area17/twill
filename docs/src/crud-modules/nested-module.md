---
pageClass: twill-doc
---

# Nested Module

To create a nested module with parent/child relationships, you should include the `laravel-nestedset` package to your application.

To install the package: `composer require kalnoy/nestedset`

Then add nested set columns to your database table.

For Laravel 5.5 and above users:

```php
Schema::create('pages', function (Blueprint $table) {
    ...
    $table->nestedSet();
});

// To drop columns
Schema::table('pages', function (Blueprint $table) {
    $table->dropNestedSet();
});
```

For prior Laravel Versions:

```php
...
use Kalnoy\Nestedset\NestedSet;

Schema::create('pages', function (Blueprint $table) {
    ...
    NestedSet::columns($table);
});

// To drop columns
Schema::table('pages', function (Blueprint $table) {
    NestedSet::dropColumns($table);
});
```

Your model should use the `Kalnoy\Nestedset\NodeTrait` trait to enable nested sets, as well as the `HasPosition` trait and some helper functions to save a new tree organisation from Twill's drag and drop UI:

```php
use A17\Twill\Models\Behaviors\HasPosition;
use Kalnoy\Nestedset\NodeTrait;
...

class Page extends Model {
    use HasPosition, NodeTrait;
    ...
    public static function saveTreeFromIds($nodeTree)
    {
        $nodeModels = self::all();
        $nodeArrays = self::flattenTree($nodeTree);

        foreach ($nodeArrays as $nodeArray) {
            $nodeModel = $nodeModels->where('id', $nodeArray['id'])->first();

            if ($nodeArray['parent_id'] === null) {
                if (!$nodeModel->isRoot() || $nodeModel->position !== $nodeArray['position']) {
                    $nodeModel->position = $nodeArray['position'];
                    $nodeModel->saveAsRoot();
                }
            } else {
                if ($nodeModel->position !== $nodeArray['position'] || $nodeModel->parent_id !== $nodeArray['parent_id']) {
                    $nodeModel->position = $nodeArray['position'];
                    $nodeModel->parent_id = $nodeArray['parent_id'];
                    $nodeModel->save();
                }
            }
        }
    }

    public static function flattenTree(array $nodeTree, int $parentId = null)
    {
        $nodeArrays = [];
        $position = 0;

        foreach ($nodeTree as $node) {
            $nodeArrays[] = [
                'id' => $node['id'],
                'position' => $position++,
                'parent_id' => $parentId,
            ];

            if (count($node['children']) > 0) {
                $childArrays = self::flattenTree($node['children'], $node['id']);
                $nodeArrays = array_merge($nodeArrays, $childArrays);
            }
        }

        return $nodeArrays;
    }
}
```

From your module's repository, you'll need to override the `setNewOrder` function:

```php
public function setNewOrder($ids)
{
    DB::transaction(function () use ($ids) {
        Page::saveTreeFromIds($ids);
    }, 3);
}
```

If you expect your users to create a lot of records, you'll want to move this operation into a queued job.

Finally, to enable Twill's nested listing UI, you'll need to do the following in your module's controller:

```php
protected $indexOptions = [
    'reorder' => true,
];

protected function indexData($request)
{
    return [
        'nested' => true,
        'nestedDepth' => 2, // this controls the allowed depth in UI
    ];
}

protected function transformIndexItems($items)
{
    return $items->toTree();
}

protected function indexItemData($item)
{
    return ($item->children ? [
        'children' => $this->getIndexTableData($item->children),
    ] : []);
}
```

When using a browser to browse a nested module, if you expect to select children as well as parents, you will need to add the following function to your module's controller:
```
protected function getBrowserItems($scopes = [])
{
    return $this->repository->get(
        $this->indexWith,
        $scopes,
        $this->orderScope(),
        request('offset') ?? $this->perPage ?? 50,
        true
    );
}
```
