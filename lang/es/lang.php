<?php

/*
|--------------------------------------------------------------------------
| 5 Steps to Contribute a New Twill Localization at Ease
|--------------------------------------------------------------------------
| 1. Find the "lang.csv" under "lang" directory.
| 2. Import the csv file into a blank Google Sheet.
| 3. Each column is a language, enter the translation for a column. (tips: feel free to freeze rows and columns).
| 4. Download the Google Sheet as CSV, replace the original "lang/lang.csv" with the new one.
| 5. Run the command "php artisan twill:lang" to sync all lang files.
*/

return [
    'auth' => [
        'email' => 'Email',
        'forgot-password' => 'Recuperar contraseña',
        'login' => 'Iniciar sesión',
        'login-title' => 'Iniciar sesión',
        'password' => 'Contraseña',
    ],
    'dashboard' => [
        'all-activity' => 'Todas las actividades',
        'create-new' => 'Crear nuevo',
        'empty-message' => 'Todavía no hay registro de actividades.',
        'my-activity' => 'Mi actividad',
        'my-drafts' => 'Mis borradores',
        'search-placeholder' => 'Buscar...',
        'statitics' => 'Estadísticas',
    ],
    'fields' => [
        'block-editor' => [
            'add-content' => 'Agregar contenido',
            'collapse-all' => 'Colapsar todo',
            'create-another' => 'Crear otro',
            'delete' => 'Borrar',
            'expand-all' => 'Expandir todo',
            'loading' => 'Cargando',
            'open-in-editor' => 'Abrir en el editor',
            'preview' => 'Previsualizar',
        ],
        'browser' => [
            'add-label' => 'Agregar',
            'attach' => 'Adjuntar',
        ],
        'files' => [
            'add-label' => 'Agregar',
        ],
        'medias' => [
            'btn-label' => 'Adjuntar imagen',
        ],
    ],
    'filter' => [
        'apply-btn' => 'Aplicar',
        'clear-btn' => 'Limpiar',
        'search-placeholder' => 'Buscar',
        'toggle-label' => 'Filtrar',
    ],
    'footer' => [
        'version' => 'Versión',
    ],
    'form' => [
        'content' => 'Contenido',
        'editor' => 'Editor',
    ],
    'listing' => [
        'add-new-button' => 'Agregar nuevo',
        'dropdown' => [
            'delete' => 'Borrar',
            'destroy' => 'Destruir',
            'duplicate' => 'Duplicar',
            'edit' => 'Editar',
            'publish' => 'Publicar',
            'feature' => 'Destacar',
            'restore' => 'Restaurar',
            'unfeature' => 'Unfeature',
            'unpublish' => 'Despublicar',
        ],
        'filter' => [
            'all-items' => 'Todos',
            'draft' => 'Borradores',
            'mine' => 'Mios',
            'published' => 'Publicados',
            'trash' => 'Borrados',
        ],
        'languages' => 'Idiomas',
        'listing-empty-message' => 'No hay registros para mostrar.',
    ],
    'main' => [
        'create' => 'Crear',
        'draft' => 'Borrador',
        'published' => 'Live',
        'title' => 'Título',
        'update' => 'Actualizar',
    ],
    'media-library' => [
        'files' => 'Archivos',
        'filter-select-label' => 'Filtrar por etiqueta',
        'images' => 'Imágenes',
        'sidebar' => [
            'clear' => 'Limpiar',
            'dimensions' => 'Dimensiones',
            'empty-text' => 'Ningún archivo seleccionado',
            'files-selected' => 'archivos seleccionados',
        ],
        'title' => 'Biblioteca Multimedia',
    ],
    'modal' => [
        'create' => [
            'button' => 'Crear',
            'create-another' => 'Crear y agregar otro',
            'title' => 'Agregar nuevo',
        ],
        'permalink-field' => 'Permalink',
        'title-field' => 'Título',
        'update' => [
            'title' => 'Actualizar',
        ],
    ],
    'nav' => [
        'admin' => 'Admin',
        'cms-users' => 'Usuarios',
        'logout' => 'Logout',
        'media-library' => 'Biblioteca Multimedia',
        'settings' => 'Configuracion',
    ],
    'publisher' => [
        'cancel' => 'Cancelar',
        'publish' => 'Publicar',
        'publish-close' => 'Publicar y cerrar',
        'publish-new' => 'Publicar y crear nuevo',
        'save' => 'Guardar como borrador',
        'save-close' => 'Guardar como borrador y cerrar',
        'save-new' => 'Guardar como borrador y crear nuevo',
        'switcher-title' => 'Estado',
        'update' => 'Actualizar',
        'update-close' => 'Actualizar y cerrar',
        'update-new' => 'Actualizar y creatr nuevo',
    ],
    'select' => [
        'empty-text' => 'No hay coincidencias.',
    ],
    'uploader' => [
        'dropzone-text' => 'o arrastre los archivos aquí.',
        'upload-btn-label' => 'Agregar nuevo',
    ],
    'user-management' => [
        'active' => 'Activar',
        'cancel' => 'Cancelar',
        'content-fieldset-label' => 'Configuración de Usuario',
        'disabled' => 'Deshabilitar',
        'edit-modal-title' => 'Editar nombre de usuario',
        'email' => 'Email',
        'enable-user' => 'Habilitar usuario',
        'enable-user-and-close' => 'Habilitar usuario y cerrar',
        'enable-user-and-create-new' => 'Habilitar usuario y crear nuevo',
        'enabled' => 'Habilitar',
        'name' => 'Nombre',
        'role' => 'Rol',
        'trash' => 'Borrados',
        'update' => 'Actualizar',
        'update-and-close' => 'Actualizar y cerrar',
        'update-and-create-new' => 'Actualizar t crear nuevo',
        'update-disabled-and-close' => 'Actualizar usuario deshabilitado y cerrar',
        'update-disabled-user' => 'Actualizar usuario deshabilitado',
        'update-disabled-user-and-create-new' => 'Actualizar usuario deshabilitado y crear nuevo',
        'user-image' => 'Imagen',
        'users' => 'Usuarios',
    ],
];
