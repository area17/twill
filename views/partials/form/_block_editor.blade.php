<hr/>

<a17-content title="Add Content"></a17-content>

@push('fieldsStore')
    window.STORE.form.content = [
        {
            title: 'Title',
            icon: 'text',
            component: 'a17-block-title'
        },
        {
            title: 'Quote',
            icon: 'quote',
            component: 'a17-block-quote'
        },
        {
            title: 'Body text',
            icon: 'text',
            component: 'a17-block-wysiwyg'
        },
        {
            title: 'Full width Image',
            icon: 'image',
            component: 'a17-block-image',
            attributes: {
                cropContext: 'cover'
            }
        },
        {
            title: 'Grid',
            icon: 'text',
            component: 'a17-block-grid'
        },
        {
            title: 'Complex block test',
            icon: 'image',
            component: 'a17-block-test'
        },
        {
            title: 'Publication Grid',
            icon: 'text',
            component: 'a17-browserfield',
            attributes: {
                max: 4,
                itemLabel: 'Publications',
                endpoint: 'https://www.mocky.io/v2/59d77e61120000ce04cb1c5b',
                modalTitle: 'Attach publications'
            }
        },
        {
            title: 'News Grid',
            icon: 'text',
            component: 'a17-browserfield',
            attributes: {
                max: 4,
                itemLabel: 'News',
                endpoint: 'https://www.mocky.io/v2/59d77e61120000ce04cb1c5b',
                modalTitle: 'News publications'
            }
        }
    ]
@endpush
