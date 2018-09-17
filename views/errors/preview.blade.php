<h2>
@if(app()->environment(['development', 'local']))
	Missing preview view, create one at resources/views/site/{{ $moduleName }}.blade.php or provide your own using the previewView property of the {{ $moduleName }} admin controller.
@else
	Previews have not been configured on this Twill module, please let the development team know about it.
@endif
</h2>
