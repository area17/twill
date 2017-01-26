<a href="{{ moduleRoute($moduleName, $routePrefix, 'edit', array_merge (isset($parent_id) ? [$parent_id] : [], [$item->id])) }}" class="main" title="Edit">
    @resourceView($moduleName, 'column')
</a>
