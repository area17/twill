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
        'email' => 'Epost',
        'password' => 'Passord',
        'forgot-password' => 'Glemt passord',
        'login' => 'Logg inn',
        'login-title' => 'Logg inn',
    ],
    'dashboard' => [
        'search-placeholder' => 'Søk alt...',
        'empty-message' => 'Du har ikke hatt noe aktivitet ennå.',
        'all-activity' => 'All aktivitet',
        'my-activity' => 'Min aktivitet',
        'create-new' => 'Opprett ny',
        'my-drafts' => 'Mine kladder',
        'statitics' => 'Statistikk',
    ],
    'footer' => [
        'version' => 'Versjon',
    ],
    'form' => [
        'content' => 'Innhold',
        'editor' => 'Editor',
    ],
    'listing' => [
        'filter' => [
            'all-items' => 'Alle elementer',
            'mine' => 'Mine',
            'published' => 'Publisert',
            'draft' => 'Kladd',
            'trash' => 'Søppel',
        ],
        'dropdown' => [
            'edit' => 'Rediger',
            'unpublish' => 'Avpubliser',
            'publish' => 'Publiser',
            'unfeature' => 'Fjern fremheving',
            'feature' => 'Fremhev',
            'restore' => 'Gjenopprett',
            'destroy' => 'Fjern',
            'delete' => 'Slett',
            'duplicate' => 'Dupliser',
        ],
        'add-new-button' => 'Opprett ny',
        'listing-empty-message' => 'Det er ingen elementer her ennå.',
        'languages' => 'Språk',
    ],
    'main' => [
        'published' => 'Publisert',
        'draft' => 'Kladd',
        'create' => 'Opprett',
        'update' => 'Endre',
        'title' => 'Tittel',
    ],
    'modal' => [
        'title-field' => 'Tittel',
        'permalink-field' => 'Permalenke',
        'create' => [
            'title' => 'Opprett ny',
            'button' => 'Opprett',
            'create-another' => 'Opprett og lag ny',
        ],
        'update' => [
            'title' => 'Oppdater',
        ],
    ],
    'nav' => [
        'media-library' => 'Mediebibliotek',
        'cms-users' => 'CMS Brukere',
        'settings' => 'Innstillinger',
        'logout' => 'Logg ut',
        'admin' => 'Administrator',
    ],
    'media-library' => [
        'title' => 'Mediebibliotek',
        'images' => 'Bilder',
        'files' => 'Filer',
        'filter-select-label' => 'Filtrer etter tag',
        'sidebar' => [
            'empty-text' => 'Ingen fil valgt',
            'files-selected' => 'filer valgt',
            'clear' => 'Tøm',
            'dimensions' => 'Dimensjoner',
            'caption' => 'Bildetekst',
            'alt-text' => 'alt tekst',
        ],
    ],
    'filter' => [
        'search-placeholder' => 'Søk',
        'toggle-label' => 'Filtrer',
        'apply-btn' => 'Bruk',
        'clear-btn' => 'Tøm',
    ],
    'select' => [
        'empty-text' => 'Beklager, ingen alternativ matcher valget.',
    ],
    'uploader' => [
        'dropzone-text' => 'eller dra- og slipp filer her',
        'upload-btn-label' => 'Last opp',
    ],
    'fields' => [
        'medias' => [
            'btn-label' => 'Legg ved bilde',
        ],
        'block-editor' => [
            'collapse-all' => 'Skjul alle',
            'expend-all' => 'Vis alle',
            'open-in-editor' => 'Åpne i editor',
            'create-another' => 'Opprett ny',
            'delete' => 'Slett',
            'add-content' => 'Legg til innhold',
            'preview' => 'Forhåndsvis',
            'loading' => 'Laster inn',
        ],
        'browser' => [
            'attach' => 'Legg ved',
            'add-label' => 'Legg til',
        ],
        'files' => [
            'add-label' => 'Legg til',
        ],
    ],
    'user-management' => [
        'users' => 'Brukere',
        'active' => 'Aktive',
        'disabled' => 'Deaktivert',
        'enabled' => 'Enabled',
        'trash' => 'Søppel',
        'user-image' => 'Bilde',
        'name' => 'Navn',
        'email' => 'Epost',
        'role' => 'Rolle',
        'content-fieldset-label' => 'Brukerinnstilling',
        'edit-modal-title' => 'Rediger brukernavn',
        'update-disabled-user' => 'Oppdater deaktivert bruker',
        'update-disabled-and-close' => 'Oppdater deaktivert bruker og lukk',
        'update-disabled-user-and-create-new' => 'Oppdater deaktivert bruker og opprett ny',
        'enable-user' => 'Aktiver bruker',
        'enable-user-and-close' => 'Aktiver bruker og lukk',
        'enable-user-and-create-new' => 'Aktiver bruker og opprett ny',
        'update' => 'Oppdater',
        'update-and-close' => 'Oppdater og lukk',
        'update-and-create-new' => 'Oppdater og opprett ny',
        'cancel' => 'Avbryt',
    ],
    'publisher' => [
        'switcher-title' => 'Status',
        'save' => 'Lagre som kladd',
        'save-close' => 'Lagre som kladd og lukk',
        'save-new' => 'Lagre som kladd og opprett ny',
        'publish' => 'Publiser',
        'publish-close' => 'Publiser og lukk',
        'publish-new' => 'Publiser og opprett ny',
        'update' => 'Oppdater',
        'update-close' => 'Oppdater og lukk',
        'update-new' => 'Oppdater og opprett ny',
        'cancel' => 'Avbryt',
    ],
];
