@formField('input', [
    'name' => $titleFormKey ?? 'title',
    'label' => $titleFormKey === 'title' ? __('twill::lang.modal.title-field') : ucfirst($titleFormKey),
    'translated' => $translateTitle ?? false,
    'required' => true,
    'onChange' => 'formatPermalink'
])

@if ($permalink ?? true)
    @formField('input', [
        'name' => 'slug',
        'label' => __('twill::lang.modal.permalink-field'),
        'translated' => true,
        'ref' => 'permalink',
        'prefix' => $permalinkPrefix ?? ''
    ])
@endif
