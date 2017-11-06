@if (isset($form_fields['id']))
    <ul>
        <li>
            <a href="{{ moduleRoute($moduleName, $routePrefix, 'edit', ['id' => $form_fields['id']]) }}"><span class="icon icon-edit"></span>Edit</a>
        </li>
    </ul>
@endif
