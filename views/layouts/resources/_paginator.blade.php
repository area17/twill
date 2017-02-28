@if($items->lastPage()>1)
    <footer>
        {{ $items->links('cms-toolkit::layouts.resources._paginator_view') }}
    </footer>
@endif
