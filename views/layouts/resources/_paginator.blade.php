@if($items->lastPage()>1)
    <footer>
        {!! $items->render(new A17\CmsToolkit\Presenters\Admin\PaginatorPresenter($items)) !!}
    </footer>
@endif
