<td>
    <a class="icon icon-trash" data-behavior="delete" data-delete-confirm="Do you really want to delete this {{ strtolower($modelName) }} ?" data-delete-reload="true" data-delete-id="{{ $item->id }}" data-delete-url="{{ moduleRoute($moduleName, $routePrefix, 'destroy', array_merge (isset($parent_id) ? [$parent_id] : [], [$item->id])) }}" href="#" rel="nofollow" title="Delete">Delete</a>
</td>
