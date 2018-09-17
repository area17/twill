@if(app()->environment(['development', 'local']))
<h2>
	Missing block's preview layout view, create one at resources/views/{{ str_replace('.', '/', $view) }}.blade.php or provide your own layout view using the block_editor.block_single_layout option in your twill.php configuration file.
</h2>
@endif
