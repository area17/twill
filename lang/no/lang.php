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
        'email' => 'Epost',
        'forgot-password' => 'Glemt passord',
        'login' => 'Logg inn',
        'login-title' => 'Logg inn',
        'password' => 'Passord',
    ],
    'dashboard' => [
        'all-activity' => 'All aktivitet',
        'create-new' => 'Opprett ny',
        'empty-message' => 'Du har ikke hatt noe aktivitet ennå.',
        'my-activity' => 'Min aktivitet',
        'my-drafts' => 'Mine kladder',
        'search-placeholder' => 'Søk alt...',
        'statitics' => 'Statistikk',
    ],
    'fields' => [
        'block-editor' => [
            'add-content' => 'Legg til innhold',
            'collapse-all' => 'Skjul alle',
            'create-another' => 'Opprett ny',
            'delete' => 'Slett',
            'expand-all' => 'Vis alle',
            'loading' => 'Laster inn',
            'open-in-editor' => 'Åpne i editor',
            'preview' => 'Forhåndsvis',
        ],
        'browser' => [
            'add-label' => 'Legg til',
            'attach' => 'Legg ved',
        ],
        'files' => [
            'add-label' => 'Legg til',
        ],
        'medias' => [
            'btn-label' => 'Legg ved bilde',
        ],
    ],
    'filter' => [
        'apply-btn' => 'Bruk',
        'clear-btn' => 'Tøm',
        'search-placeholder' => 'Søk',
        'toggle-label' => 'Filtrer',
    ],
    'footer' => [
        'version' => 'Versjon',
    ],
    'form' => [
        'content' => 'Innhold',
        'editor' => 'Editor',
    ],
    'listing' => [
        'add-new-button' => 'Opprett ny',
        'dropdown' => [
            'delete' => 'Slett',
            'destroy' => 'Fjern',
            'duplicate' => 'Dupliser',
            'edit' => 'Rediger',
            'publish' => 'Publiser',
            'feature' => 'Fremhev',
            'restore' => 'Gjenopprett',
            'unfeature' => 'Fjern fremheving',
            'unpublish' => 'Avpubliser',
        ],
        'filter' => [
            'all-items' => 'Alle elementer',
            'draft' => 'Kladd',
            'mine' => 'Mine',
            'published' => 'Publisert',
            'trash' => 'Søppel',
        ],
        'languages' => 'Språk',
        'listing-empty-message' => 'Det er ingen elementer her ennå.',
    ],
    'main' => [
        'create' => 'Opprett',
        'draft' => 'Kladd',
        'published' => 'Publisert',
        'title' => 'Tittel',
        'update' => 'Endre',
    ],
    'media-library' => [
        'files' => 'Filer',
        'filter-select-label' => 'Filtrer etter tag',
        'images' => 'Bilder',
        'sidebar' => [
            'alt-text' => 'alt tekst',
            'caption' => 'Bildetekst',
            'clear' => 'Tøm',
            'dimensions' => 'Dimensjoner',
            'empty-text' => 'Ingen fil valgt',
            'files-selected' => 'filer valgt',
        ],
        'title' => 'Mediebibliotek',
    ],
    'modal' => [
        'create' => [
            'button' => 'Opprett',
            'create-another' => 'Opprett og lag ny',
            'title' => 'Opprett ny',
        ],
        'permalink-field' => 'Permalenke',
        'title-field' => 'Tittel',
        'update' => [
            'title' => 'Oppdater',
        ],
    ],
    'nav' => [
        'admin' => 'Administrator',
        'cms-users' => 'CMS Brukere',
        'logout' => 'Logg ut',
        'media-library' => 'Mediebibliotek',
        'settings' => 'Innstillinger',
    ],
    'publisher' => [
        'cancel' => 'Avbryt',
        'publish' => 'Publiser',
        'publish-close' => 'Publiser og lukk',
        'publish-new' => 'Publiser og opprett ny',
        'save' => 'Lagre som kladd',
        'save-close' => 'Lagre som kladd og lukk',
        'save-new' => 'Lagre som kladd og opprett ny',
        'switcher-title' => 'Status',
        'update' => 'Oppdater',
        'update-close' => 'Oppdater og lukk',
        'update-new' => 'Oppdater og opprett ny',
    ],
    'select' => [
        'empty-text' => 'Beklager, ingen alternativ matcher valget.',
    ],
    'uploader' => [
        'dropzone-text' => 'eller dra- og slipp filer her',
        'upload-btn-label' => 'Last opp',
    ],
    'user-management' => [
        'active' => 'Aktive',
        'cancel' => 'Avbryt',
        'content-fieldset-label' => 'Brukerinnstilling',
        'disabled' => 'Deaktivert',
        'edit-modal-title' => 'Rediger brukernavn',
        'email' => 'E-post',
        'enable-user' => 'Aktiver bruker',
        'enable-user-and-close' => 'Aktiver bruker og lukk',
        'enable-user-and-create-new' => 'Aktiver bruker og opprett ny',
        'enabled' => 'Enabled',
        'name' => 'Navn',
        'role' => 'Rolle',
        'trash' => 'Søppel',
        'update' => 'Oppdater',
        'update-and-close' => 'Oppdater og lukk',
        'update-and-create-new' => 'Oppdater og opprett ny',
        'update-disabled-and-close' => 'Oppdater deaktivert bruker og lukk',
        'update-disabled-user' => 'Oppdater deaktivert bruker',
        'update-disabled-user-and-create-new' => 'Oppdater deaktivert bruker og opprett ny',
        'user-image' => 'Bilde',
        'users' => 'Brukere',
    ],
];
