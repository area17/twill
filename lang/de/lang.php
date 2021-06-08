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
        'forgot-password' => 'Passwort vergessen',
        'login' => 'Anmelden',
        'login-title' => 'Anmelden',
        'password' => 'Passwort',
    ],
    'dashboard' => [
        'all-activity' => 'Alle aktivitäten',
        'create-new' => 'Neu erstellen',
        'empty-message' => 'Du hast noch keine Aktivitäten.',
        'my-activity' => 'Meine Aktivitäten',
        'my-drafts' => 'Meine Entwürfe',
        'search-placeholder' => 'Alles durchsuchen ...',
        'statitics' => 'Statistiken',
    ],
    'fields' => [
        'block-editor' => [
            'add-content' => 'Inhalt hinzufügen',
            'collapse-all' => 'Alle einklappen',
            'create-another' => 'Noch einen erstellen',
            'delete' => 'Löschen',
            'expand-all' => 'Alle ausklappen',
            'loading' => 'Lade',
            'open-in-editor' => 'In Editor öffnen',
            'preview' => 'Vorschau',
        ],
        'browser' => [
            'add-label' => 'Hinzufügen',
            'attach' => 'Anhängen',
        ],
        'files' => [
            'add-label' => 'Hinzufügen',
        ],
        'medias' => [
            'btn-label' => 'Bild anhängen',
        ],
    ],
    'filter' => [
        'apply-btn' => 'Anwenden',
        'clear-btn' => 'Leeren',
        'search-placeholder' => 'Suchen',
        'toggle-label' => 'Filter',
    ],
    'footer' => [
        'version' => 'Version',
    ],
    'form' => [
        'content' => 'Inhalt',
        'editor' => 'Editor',
    ],
    'listing' => [
        'add-new-button' => 'Neu erstellen',
        'dropdown' => [
            'delete' => 'Löschen',
            'destroy' => 'Vernichten',
            'duplicate' => 'Duplizieren',
            'edit' => 'Bearbeiten',
            'publish' => 'Veröffentlichen',
            'feature' => 'Hervorheben',
            'restore' => 'Wiederherstellen',
            'unfeature' => 'Hervorhebung aufheben',
            'unpublish' => 'Veröffentlichung aufheben',
        ],
        'filter' => [
            'all-items' => 'Alle Ressourcen',
            'draft' => 'Entwurf',
            'mine' => 'Meine',
            'published' => 'Veröffentlicht',
            'trash' => 'Papierkorb',
        ],
        'languages' => 'Sprachen',
        'listing-empty-message' => 'Es existieren hier noch keine Ressourcen',
    ],
    'main' => [
        'create' => 'Erstellen',
        'draft' => 'Entwurf',
        'published' => 'Live',
        'title' => 'Titel',
        'update' => 'Aktualisieren',
    ],
    'media-library' => [
        'files' => 'Dateien',
        'filter-select-label' => 'Nach Tags filtern',
        'images' => 'Bilder',
        'sidebar' => [
            'clear' => 'Leeren',
            'dimensions' => 'Dimensionen',
            'empty-text' => 'Keine Datei ausgewählt',
            'files-selected' => 'Dateien ausgewählt',
        ],
        'title' => 'Medienverzeichnis',
    ],
    'modal' => [
        'create' => [
            'button' => 'Erstellen',
            'create-another' => 'Erstellen und noch etwas hinzufügen',
            'title' => 'Neu erstellen',
        ],
        'permalink-field' => 'Permalink',
        'title-field' => 'Titel',
        'update' => [
            'title' => 'Aktualisieren',
        ],
    ],
    'nav' => [
        'admin' => 'Admin',
        'cms-users' => 'CMS Benutzer',
        'logout' => 'Abmelden',
        'media-library' => 'Medienverzeichnis',
        'settings' => 'Einstellungen',
    ],
    'publisher' => [
        'cancel' => 'Abbrechen',
        'publish' => 'Veröffentlichen',
        'publish-close' => 'Veröffentlichen und schließen',
        'publish-new' => 'Veröffentlichen und neu erstellen',
        'save' => 'Als Entwurf speichern',
        'save-close' => 'Als Entwurf speichern und schließen',
        'save-new' => 'Als Entwurf speichern und neu erstellen',
        'switcher-title' => 'Status',
        'update' => 'Aktualisieren',
        'update-close' => 'Aktualisieren und schließen',
        'update-new' => 'Aktualisieren und neu erstellen',
    ],
    'select' => [
        'empty-text' => 'Entschuldige, keine treffenden Optionen',
    ],
    'uploader' => [
        'dropzone-text' => 'oder neue Dateien hier ablegen',
        'upload-btn-label' => 'Neu hinzufügen',
    ],
    'user-management' => [
        'active' => 'Aktiv',
        'cancel' => 'Abbrechen',
        'content-fieldset-label' => 'Benutzer Einstellungen',
        'disabled' => 'Deaktiviert',
        'edit-modal-title' => 'Benutzername bearbeiten',
        'email' => 'Email',
        'enable-user' => 'Benutzer aktivieren',
        'enable-user-and-close' => 'Benutzer aktivieren und schließen',
        'enable-user-and-create-new' => 'Benutzer aktivieren und neu erstellen',
        'enabled' => 'Aktiviert',
        'name' => 'Name',
        'role' => 'Rolle',
        'trash' => 'Papierkorb',
        'update' => 'Aktualisieren',
        'update-and-close' => 'Aktualisieren und schließen',
        'update-and-create-new' => 'Aktualisieren und neu erstellen',
        'update-disabled-and-close' => 'Deaktivierten aktualisieren und schließen',
        'update-disabled-user' => 'Deaktivierten Benutzer aktualisieren',
        'update-disabled-user-and-create-new' => 'Deaktivierten Benutzer aktualisieren und neuen erstellen',
        'user-image' => 'Bild',
        'users' => 'Benutzer',
    ],
];
