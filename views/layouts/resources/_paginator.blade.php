@if($items->lastPage()>1)
    <footer>
        {{ $items->appends(Input::except('page'))->links('cms-toolkit::layouts.resources._paginator_view') }}
    </footer>
@endif
