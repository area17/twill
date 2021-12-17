@if(app()->environment(['development', 'local']))
    @if ($error)
        <h2>An error occured trying to render preview:</h2>
        <p>{{$error}}</p>
    @else
        <h2>
            Missing block's preview view, create one at resources/views/{{ str_replace('.', '/', $view) }}.blade.php or provide your own blocks views path using the block_editor.block_views_path option in your twill.php configuration file.
        </h2>
    @endif
@endif
