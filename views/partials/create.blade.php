@formField('input', [
    'name' => $titleFormKey ?? 'title',
    'label' => ucfirst($titleFormKey ?? 'title'),
    'translated' => $translateTitle ?? false,
    'required' => true,
    {{-- 'onChange' => 'formatPermalink' --}}
])

{{-- @formField('input', [
    'name' => 'permalink',
    'label' => 'Permalink',
    'translated' => true,
    'ref' => 'permalink'
]) --}}
