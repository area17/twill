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
        'forgot-password' => 'Password dimenticata?',
        'login' => 'Login',
        'login-title' => 'Login',
        'password' => 'Password',
    ],
    'dashboard' => [
        'all-activity' => 'Tutte le attività',
        'create-new' => 'Crea nuovo',
        'empty-message' => 'Non hai ancora nessuna attività',
        'my-activity' => 'Le mie attività',
        'my-drafts' => 'Le mie bozze',
        'search-placeholder' => 'Ricerca in tutto il database...',
        'statitics' => 'Statistiche',
    ],
    'fields' => [
        'block-editor' => [
            'add-content' => 'Aggiungi contenuto',
            'collapse-all' => 'Racchiudi tutto',
            'create-another' => 'Crea e aggiungi ancora',
            'delete' => 'Elimina',
            'expand-all' => 'Espandi tutto',
            'loading' => 'Caricamento',
            'open-in-editor' => 'Apri in modifica',
            'preview' => 'Anteprima',
        ],
        'browser' => [
            'add-label' => 'Aggiungi',
            'attach' => 'Allega',
        ],
        'files' => [
            'add-label' => 'Aggiungi',
        ],
        'medias' => [
            'btn-label' => 'Allega immagine',
        ],
    ],
    'filter' => [
        'apply-btn' => 'Applica',
        'clear-btn' => 'Cancella',
        'search-placeholder' => 'Cerca',
        'toggle-label' => 'Filtro',
    ],
    'footer' => [
        'version' => 'Versione',
    ],
    'form' => [
        'content' => 'Contenuto',
        'editor' => 'Editor',
    ],
    'listing' => [
        'add-new-button' => 'Nuovo',
        'dropdown' => [
            'delete' => 'Elimina',
            'destroy' => 'Distruggi',
            'duplicate' => 'Duplica',
            'edit' => 'Modifica',
            'publish' => 'Pubblica',
            'feature' => 'Promuovi',
            'restore' => 'Ripristina',
            'unfeature' => 'Non promuovere',
            'unpublish' => 'Spubblica',
        ],
        'filter' => [
            'all-items' => 'Tutti',
            'draft' => 'Bozza',
            'mine' => 'Miei',
            'published' => 'Pubblicati',
            'trash' => 'Cestino',
        ],
        'languages' => 'Lingue',
        'listing-empty-message' => 'Non è presente alcun elemento.',
    ],
    'main' => [
        'create' => 'Crea',
        'draft' => 'Bozza',
        'published' => 'Pubblicato',
        'title' => 'Titolo',
        'update' => 'Aggiorna',
    ],
    'media-library' => [
        'files' => 'Documenti',
        'filter-select-label' => 'Filtra per Tag',
        'images' => 'Immagini',
        'sidebar' => [
            'clear' => 'Cancella',
            'dimensions' => 'Dimensioni',
            'empty-text' => 'Nessun file selezionato',
            'files-selected' => 'file selezionati',
        ],
        'title' => 'Libreria',
    ],
    'modal' => [
        'create' => [
            'button' => 'Crea',
            'create-another' => 'Crea e aggiungi ancora',
            'title' => 'Nuovo',
        ],
        'permalink-field' => 'Permalink',
        'title-field' => 'Titolo',
        'update' => [
            'title' => 'Aggiurna',
        ],
    ],
    'nav' => [
        'admin' => 'Admin',
        'cms-users' => 'CMS Utenti',
        'logout' => 'Esci',
        'media-library' => 'Libreria',
        'settings' => 'Impostazioni',
    ],
    'publisher' => [
        'cancel' => 'Annulla',
        'publish' => 'Pubblica',
        'publish-close' => 'Pubblica e chiudi',
        'publish-new' => 'Pubblica e crea nuovo',
        'save' => 'Salva come Bozza',
        'save-close' => 'Salva come Bozza e chiudi',
        'save-new' => 'Salva come bozza e crea nuovo',
        'switcher-title' => 'Stato',
        'update' => 'Aggiorna',
        'update-close' => 'Aggiorna e chiudi',
        'update-new' => 'Aggiorna e crea nuovo',
    ],
    'select' => [
        'empty-text' => '...nessuna opzione',
    ],
    'uploader' => [
        'dropzone-text' => 'o trascina i file qui',
        'upload-btn-label' => 'Aggiungi',
    ],
    'user-management' => [
        'active' => 'Attivi',
        'cancel' => 'Annulla',
        'content-fieldset-label' => 'Impostazioni utente',
        'disabled' => 'Disabilitati',
        'edit-modal-title' => 'Modifica il nominativo',
        'email' => 'Email',
        'enable-user' => 'Abilita',
        'enable-user-and-close' => 'Abilita e chiudi',
        'enable-user-and-create-new' => 'Abilita e crea nuovo',
        'enabled' => 'Abilitati',
        'name' => 'Nominativo',
        'role' => 'Ruolo',
        'trash' => 'Cestino',
        'update' => 'Aggiorna',
        'update-and-close' => 'Aggiorna e chiudi',
        'update-and-create-new' => 'Aggiorna e crea nuovo',
        'update-disabled-and-close' => 'Disabilita e chiudi',
        'update-disabled-user' => 'Disabilita',
        'update-disabled-user-and-create-new' => 'Disabilita e crea nuovo',
        'user-image' => 'Immagine utente',
        'users' => 'Utenti',
    ],
];
