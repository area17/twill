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
        'password' => 'Password',
        'forgot-password' => 'Password dimenticata?',
        'login' => 'Login',
        'login-title' => 'Login',
    ],
    'dashboard' => [
        'search-placeholder' => 'Ricerca in tutto il database...',
        'empty-message' => 'Non hai ancora nessuna attività',
        'all-activity' => 'Tutte le attività',
        'my-activity' => 'Le mie attività',
        'create-new' => 'Crea nuovo',
        'my-drafts' => 'Le mie bozze',
        'statitics' => 'Statistiche',
    ],
    'footer' => [
        'version' => 'Versione',
    ],
    'form' => [
        'content' => 'Contenuto',
        'editor' => 'Editor',
    ],
    'listing' => [
        'filter' => [
            'all-items' => 'Tutti',
            'mine' => 'Miei',
            'published' => 'Pubblicati',
            'draft' => 'Bozza',
            'trash' => 'Cestino',
        ],
        'dropdown' => [
            'edit' => 'Modifica',
            'unpublish' => 'Spubblica',
            'publish' => 'Pubblica',
            'unfeature' => 'Non promuovere',
            'feature' => 'Promuovi',
            'restore' => 'Ripristina',
            'destroy' => 'Distruggi',
            'delete' => 'Elimina',
            'duplicate' => 'Duplica',
        ],
        'add-new-button' => 'Nuovo',
        'listing-empty-message' => 'Non è presente alcun elemento.',
        'languages' => 'Lingue',
    ],
    'main' => [
        'published' => 'Pubblicato',
        'draft' => 'Bozza',
        'create' => 'Crea',
        'update' => 'Aggiorna',
        'title' => 'Titolo',
    ],
    'modal' => [
        'title-field' => 'Titolo',
        'permalink-field' => 'Permalink',
        'create' => [
            'title' => 'Nuovo',
            'button' => 'Crea',
            'create-another' => 'Crea e aggiungi ancora',
        ],
        'update' => [
            'title' => 'Aggiurna',
        ],
    ],
    'nav' => [
        'media-library' => 'Libreria',
        'cms-users' => 'CMS Utenti',
        'settings' => 'Impostazioni',
        'logout' => 'Esci',
        'admin' => 'Admin',
    ],
    'media-library' => [
        'title' => 'Libreria',
        'images' => 'Immagini',
        'files' => 'Documenti',
        'filter-select-label' => 'Filtra per Tag',
        'sidebar' => [
            'empty-text' => 'Nessun file selezionato',
            'files-selected' => 'file selezionati',
            'clear' => 'Cancella',
            'dimensions' => 'Dimensioni',
        ],
    ],
    'filter' => [
        'search-placeholder' => 'Cerca',
        'toggle-label' => 'Filtro',
        'apply-btn' => 'Applica',
        'clear-btn' => 'Cancella',
    ],
    'select' => [
        'empty-text' => '...nessuna opzione',
    ],
    'uploader' => [
        'dropzone-text' => 'o trascina i file qui',
        'upload-btn-label' => 'Aggiungi',
    ],
    'fields' => [
        'medias' => [
            'btn-label' => 'Allega immagine',
        ],
        'block-editor' => [
            'collapse-all' => 'Racchiudi tutto',
            'expand-all' => 'Espandi tutto',
            'open-in-editor' => 'Apri in modifica',
            'create-another' => 'Crea e aggiungi ancora',
            'delete' => 'Elimina',
            'add-content' => 'Aggiungi contenuto',
            'preview' => 'Anteprima',
            'loading' => 'Caricamento',
        ],
        'browser' => [
            'attach' => 'Allega',
            'add-label' => 'Aggiungi',
        ],
        'files' => [
            'add-label' => 'Aggiungi',
        ],
    ],
    'user-management' => [
        'users' => 'Utenti',
        'active' => 'Attivi',
        'disabled' => 'Disabilitati',
        'enabled' => 'Abilitati',
        'trash' => 'Cestino',
        'user-image' => 'Immagine utente',
        'name' => 'Nominativo',
        'email' => 'Email',
        'role' => 'Ruolo',
        'content-fieldset-label' => 'Impostazioni utente',
        'edit-modal-title' => 'Modifica il nominativo',
        'update-disabled-user' => 'Disabilita',
        'update-disabled-and-close' => 'Disabilita e chiudi',
        'update-disabled-user-and-create-new' => 'Disabilita e crea nuovo',
        'enable-user' => 'Abilita',
        'enable-user-and-close' => 'Abilita e chiudi',
        'enable-user-and-create-new' => 'Abilita e crea nuovo',
        'update' => 'Aggiorna',
        'update-and-close' => 'Aggiorna e chiudi',
        'update-and-create-new' => 'Aggiorna e crea nuovo',
        'cancel' => 'Annulla',
    ],
    'publisher' => [
        'switcher-title' => 'Stato',
        'save' => 'Salva come Bozza',
        'save-close' => 'Salva come Bozza e chiudi',
        'save-new' => 'Salva come bozza e crea nuovo',
        'publish' => 'Pubblica',
        'publish-close' => 'Pubblica e chiudi',
        'publish-new' => 'Pubblica e crea nuovo',
        'update' => 'Aggiorna',
        'update-close' => 'Aggiorna e chiudi',
        'update-new' => 'Aggiorna e crea nuovo',
        'cancel' => 'Annulla',
    ],
];
