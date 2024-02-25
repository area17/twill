<?php
return [
    'auth' => [
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
        'email' => 'Email',
        'forgot-password' => 'Recuperar contraseña',
        'login' => 'Iniciar sesión',
        'login-title' => 'Iniciar sesión',
        'password' => 'Contraseña',
        'choose-password' => 'Elija una contraseña',
        'oauth-link-title' => 'Vuelva a introducir su contraseña para vincular :provider a su cuenta',
        'otp' => 'Contraseña de un solo uso',
        'password-confirmation' => 'Confirmar la contraseña',
        'reset-password' => 'Restablecer la contraseña',
        'reset-send' => 'Enviar un enlace para restablecer la contraseña',
        'verify-login' => 'Confirmar inicio de sesión',
        'auth-causer' => 'Autentificación',
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
        'back-to-login' => 'Volver al inicio de sesión',
    ],
    'dashboard' => [
        'all-activity' => 'Todas las actividades',
        'create-new' => 'Crear nuevo',
        'empty-message' => 'Todavía no hay registro de actividades.',
        'my-activity' => 'Mi actividad',
        'my-drafts' => 'Mis borradores',
        'search-placeholder' => 'Buscar...',
        'statitics' => 'Estadísticas',
        'activities' => [
            'unpublished' => 'Sin publicar',
            'published' => 'Publicado',
            'featured' => 'Destacados',
            'unfeatured' => 'Sin destacar',
            'restored' => 'Restaurado',
            'deleted' => 'Borrado',
            'login' => 'Iniciar sesión',
            'logout' => 'Cerrar sesión',
            'created' => 'Creado',
            'updated' => 'Actualizado',
        ],
        'activity-row' => [
            'edit' => 'Editar',
            'view-permalink' => 'Ver enlace permanente',
            'by' => 'por',
        ],
        'unknown-author' => 'Desconocido',
        'search' => [
            'loading' => 'Cargando…',
            'no-result' => 'No hay resultados.',
            'last-edit' => 'Última edición',
        ],
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
            'clone-block' => 'Clonar bloque',
            'add-item' => 'Añadir elemento',
            'select-existing' => 'Seleccionar los existentes',
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
        'generic' => [
            'switch-language' => 'Cambiar de idioma',
        ],
        'map' => [
            'hide' => 'Ocultar&nbsp;mapa',
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
    'buckets' => [
        'intro' => '¿Qué le gustaría presentar hoy?',
        'none-available' => 'Ningún artículo disponible.',
        'none-featured' => 'No hay artículos destacados.',
        'publish' => 'Publicar',
        'source-title' => 'Elementos disponibles',
    ],
    'dialog' => [
        'cancel' => 'Cancelar',
        'ok' => 'De acuerdo',
        'title' => 'Mover a la papelera',
    ],
    'editor' => [
        'cancel' => 'Cancelar',
        'done' => 'Hecho',
        'title' => 'Editor de contenidos',
        'delete' => 'Borrar',
    ],
    'emails' => [
        'all-rights-reserved' => 'Todos los derechos reservados.',
        'hello' => '¡Hola!',
        'problems' => 'Si tiene problemas para hacer clic en el botón ":actionText", copie y pegue la siguiente URL en su navegador web: [:url](:url)',
        'regards' => 'Saludos,',
    ],
];
