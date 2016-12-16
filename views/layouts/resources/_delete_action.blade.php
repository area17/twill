<td>
    <a class="icon icon-trash" data-behavior="delete" data-delete-confirm="Do you really want to delete this {{ strtolower($modelName) }} ?" data-delete-reload="true" data-delete-id="{{ $item->id }}" data-delete-url="{{ moduleRoute($moduleName, $routePrefix, 'destroy', $item->id) }}" href="#" rel="nofollow" title="Delete">Delete</a>
</td>
