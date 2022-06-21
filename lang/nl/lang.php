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
        'email' => 'E-mail',
        'forgot-password' => 'Wachtwoord vergeten',
        'login' => 'Login',
        'login-title' => 'Login',
        'password' => 'Wachtwoord',
    ],
    'dashboard' => [
        'all-activity' => 'Alle activiteit',
        'create-new' => 'Nieuw maken',
        'empty-message' => 'U heeft nog geen activiteiten.',
        'my-activity' => 'Mijn activiteit',
        'my-drafts' => 'Mijn concepten',
        'search-placeholder' => 'Zoek in alles...',
        'statitics' => 'Statistieken',
    ],
    'fields' => [
        'block-editor' => [
            'add-content' => 'Inhoud toevoegen',
            'collapse-all' => 'Alles inklappen',
            'create-another' => 'Nieuw maken',
            'delete' => 'Verwijderen',
            'expand-all' => 'Alles uitklappen',
            'loading' => 'Laden',
            'open-in-editor' => 'Open in editor',
            'preview' => 'Voorbeeld',
        ],
        'browser' => [
            'add-label' => 'Toevoegen',
            'attach' => 'Bijvoegen',
        ],
        'files' => [
            'add-label' => 'Toevoegen',
        ],
        'medias' => [
            'btn-label' => 'Afbeelding bijvoegen',
        ],
    ],
    'filter' => [
        'apply-btn' => 'Toepassen',
        'clear-btn' => 'Leegmaken',
        'search-placeholder' => 'Zoeken',
        'toggle-label' => 'Filteren',
    ],
    'footer' => [
        'version' => 'Versie',
    ],
    'form' => [
        'content' => 'Inhoud',
        'editor' => 'Editor',
    ],
    'listing' => [
        'add-new-button' => 'Nieuw toevoegen',
        'dropdown' => [
            'delete' => 'Verwijderen',
            'destroy' => 'Vernietigen',
            'duplicate' => 'Dupliceren',
            'edit' => 'Bewerken',
            'publish' => 'Publiceren',
            'feature' => 'Markeren',
            'restore' => 'Herstellen',
            'unfeature' => 'Demarkeren',
            'unpublish' => 'Depubliceren',
        ],
        'filter' => [
            'all-items' => 'Alle items',
            'draft' => 'Concept',
            'mine' => 'Van mij',
            'published' => 'Gepubliceerd',
            'trash' => 'Prullenbak',
        ],
        'languages' => 'Talen',
        'listing-empty-message' => 'Er is nog geen item hier.',
    ],
    'main' => [
        'create' => 'Aanmaken',
        'draft' => 'Concept',
        'published' => 'Live',
        'title' => 'Titel',
        'update' => 'Bijwerken',
    ],
    'media-library' => [
        'files' => 'Bestanden',
        'filter-select-label' => 'Op tags filteren',
        'images' => 'Afbeeldingen',
        'sidebar' => [
            'clear' => 'Leegmaken',
            'dimensions' => 'Dimensies',
            'empty-text' => 'Geen bestand geselecteerd',
            'files-selected' => 'bestanden geselecteerd',
        ],
        'title' => 'Mediabibliotheek',
    ],
    'modal' => [
        'create' => [
            'button' => 'Aanmaken',
            'create-another' => 'Aanmaken en andere toevoegen',
            'title' => 'Nieuw toevoegen',
        ],
        'permalink-field' => 'Permalink',
        'title-field' => 'Titel',
        'update' => [
            'title' => 'Bijwerken',
        ],
    ],
    'nav' => [
        'admin' => 'Admin',
        'cms-users' => 'CMS-gebruikers',
        'logout' => 'Uitloggen',
        'media-library' => 'Mediabibliotheek',
        'settings' => 'Instellingen',
        'profile' => 'Profile',
    ],
    'publisher' => [
        'cancel' => 'Annuleren',
        'publish' => 'Publiceren',
        'publish-close' => 'Publiceren en sluiten',
        'publish-new' => 'Publiceren en nieuwe aanmaken',
        'save' => 'Opslaan als concept',
        'save-close' => 'Opslaan als concept en sluiten',
        'save-new' => 'Opslaan als concept en nieuwe aanmaken',
        'switcher-title' => 'Status',
        'update' => 'Bijwerken',
        'update-close' => 'Bijwerken en sluiten',
        'update-new' => 'Bijwerken en nieuwe aanmaken',
    ],
    'select' => [
        'empty-text' => 'Sorry, geen overeenkomstige opties.',
    ],
    'uploader' => [
        'dropzone-text' => 'of sleep nieuwe bestanden naar hier',
        'upload-btn-label' => 'Nieuw toevoegen',
    ],
    'user-management' => [
        'active' => 'Actief',
        'cancel' => 'Annuleren',
        'content-fieldset-label' => 'Gebruikersinstellingen',
        'disabled' => 'Uitgeschakeld',
        'edit-modal-title' => 'Bewerk gebruikersnaam',
        'email' => 'E-mail',
        'enable-user' => 'Gebruiker inschakelen',
        'enable-user-and-close' => 'Gebruiker inschakelen en sluiten',
        'enable-user-and-create-new' => 'Gebruiker inschakelen en nieuwe aanmaken',
        'enabled' => 'Ingeschakeld',
        'name' => 'Naam',
        'role' => 'Rol',
        'trash' => 'Prullenbak',
        'update' => 'Bijwerken',
        'update-and-close' => 'Bijwerken en sluiten',
        'update-and-create-new' => 'Bijwerken en nieuwe aanmaken',
        'update-disabled-and-close' => 'Uitgeschakeld bijwerken en sluiten',
        'update-disabled-user' => 'Uitgeschakelde gebruiker bijwerken',
        'update-disabled-user-and-create-new' => 'Uitgeschakelde gebruiker bijwerken en nieuwe aanmaken',
        'user-image' => 'Afbeelding',
        'users' => 'Gebruikers',
    ],
];
