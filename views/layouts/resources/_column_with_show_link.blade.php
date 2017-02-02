<a href="{{ moduleRoute($moduleName, $routePrefix, 'show', array_merge (isset($parent_id) ? [$parent_id] : [], [$item->id])) }}" class="main" title="SHow">
    @resourceView($moduleName, 'column')
</a>
