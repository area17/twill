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
        'password' => 'Passwort',
        'forgot-password' => 'Passwort vergessen',
        'login' => 'Anmelden',
        'login-title' => 'Anmelden',
    ],
    'dashboard' => [
        'search-placeholder' => 'Alles durchsuchen ...',
        'empty-message' => 'Du hast noch keine Aktivitäten.',
        'all-activity' => 'Alle aktivitäten',
        'my-activity' => 'Meine Aktivitäten',
        'create-new' => 'Neu erstellen',
        'my-drafts' => 'Meine Entwürfe',
        'statitics' => 'Statistiken',
    ],
    'footer' => [
        'version' => 'Version',
    ],
    'form' => [
        'content' => 'Inhalt',
        'editor' => 'Editor',
    ],
    'listing' => [
        'filter' => [
            'all-items' => 'Alle Ressourcen',
            'mine' => 'Meine',
            'published' => 'Veröffentlicht',
            'draft' => 'Entwurf',
            'trash' => 'Papierkorb',
        ],
        'dropdown' => [
            'edit' => 'Bearbeiten',
            'unpublish' => 'Veröffentlichung aufheben',
            'publish' => 'Veröffentlichen',
            'unfeature' => 'Hervorhebung aufheben',
            'feature' => 'Hervorheben',
            'restore' => 'Wiederherstellen',
            'destroy' => 'Vernichten',
            'delete' => 'Löschen',
            'duplicate' => 'Duplizieren',
        ],
        'add-new-button' => 'Neu erstellen',
        'listing-empty-message' => 'Es existieren hier noch keine Ressourcen',
        'languages' => 'Sprachen',
    ],
    'main' => [
        'published' => 'Live',
        'draft' => 'Entwurf',
        'create' => 'Erstellen',
        'update' => 'Aktualisieren',
        'title' => 'Titel',
    ],
    'modal' => [
        'title-field' => 'Titel',
        'permalink-field' => 'Permalink',
        'create' => [
            'title' => 'Neu erstellen',
            'button' => 'Erstellen',
            'create-another' => 'Erstellen und noch etwas hinzufügen',
        ],
        'update' => [
            'title' => 'Aktualisieren',
        ],
    ],
    'nav' => [
        'media-library' => 'Medienverzeichnis',
        'cms-users' => 'CMS Benutzer',
        'settings' => 'Einstellungen',
        'logout' => 'Abmelden',
        'admin' => 'Admin',
    ],
    'media-library' => [
        'title' => 'Medienverzeichnis',
        'images' => 'Bilder',
        'files' => 'Dateien',
        'filter-select-label' => 'Nach Tags filtern',
        'sidebar' => [
            'empty-text' => 'Keine Datei ausgewählt',
            'files-selected' => 'Dateien ausgewählt',
            'clear' => 'Leeren',
            'dimensions' => 'Dimensionen',
        ],
    ],
    'filter' => [
        'search-placeholder' => 'Suchen',
        'toggle-label' => 'Filter',
        'apply-btn' => 'Anwenden',
        'clear-btn' => 'Leeren',
    ],
    'select' => [
        'empty-text' => 'Entschuldige, keine treffenden Optionen',
    ],
    'uploader' => [
        'dropzone-text' => 'oder neue Dateien hier ablegen',
        'upload-btn-label' => 'Neu hinzufügen',
    ],
    'fields' => [
        'medias' => [
            'btn-label' => 'Bild anhängen',
        ],
        'block-editor' => [
            'collapse-all' => 'Alle einklappen',
            'expend-all' => 'Alle ausklappen',
            'open-in-editor' => 'In Editor öffnen',
            'create-another' => 'Noch einen erstellen',
            'delete' => 'Löschen',
            'add-content' => 'Inhalt hinzufügen',
            'preview' => 'Vorschau',
            'loading' => 'Lade',
        ],
        'browser' => [
            'attach' => 'Anhängen',
            'add-label' => 'Hinzufügen',
        ],
        'files' => [
            'add-label' => 'Hinzufügen',
        ],
    ],
    'user-management' => [
        'users' => 'Benutzer',
        'active' => 'Aktiv',
        'disabled' => 'Deaktiviert',
        'enabled' => 'Aktiviert',
        'trash' => 'Papierkorb',
        'user-image' => 'Bild',
        'name' => 'Name',
        'email' => 'Email',
        'role' => 'Rolle',
        'content-fieldset-label' => 'Benutzer Einstellungen',
        'edit-modal-title' => 'Benutzername bearbeiten',
        'update-disabled-user' => 'Deaktivierten Benutzer aktualisieren',
        'update-disabled-and-close' => 'Deaktivierten aktualisieren und schließen',
        'update-disabled-user-and-create-new' => 'Deaktivierten Benutzer aktualisieren und neuen erstellen',
        'enable-user' => 'Benutzer aktivieren',
        'enable-user-and-close' => 'Benutzer aktivieren und schließen',
        'enable-user-and-create-new' => 'Benutzer aktivieren und neu erstellen',
        'update' => 'Aktualisieren',
        'update-and-close' => 'Aktualisieren und schließen',
        'update-and-create-new' => 'Aktualisieren und neu erstellen',
        'cancel' => 'Abbrechen',
    ],
    'publisher' => [
        'switcher-title' => 'Status',
        'save' => 'Als Entwurf speicheren',
        'save-close' => 'Als Entwurf speicheren und schließen',
        'save-new' => 'Als Entwurf speicheren und neu erstellen',
        'publish' => 'Veröffentlichen',
        'publish-close' => 'Veröffentlichen und schließen',
        'publish-new' => 'Veröffentlichen und neu erstellen',
        'update' => 'Aktualisieren',
        'update-close' => 'Aktualisieren und schließen',
        'update-new' => 'Aktualisieren und neu erstellen',
        'cancel' => 'Abbrechen',
    ],
];
