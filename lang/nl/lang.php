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
        'back-to-login' => 'Terug naar login',
        'choose-password' => 'Wachtwoord kiezen',
        'email' => 'E-mail',
        'forgot-password' => 'Wachtwoord vergeten',
        'login' => 'Login',
        'login-title' => 'Login',
        'oauth-link-title' => 'Voer je wachtwoord opnieuw in om :provider met je account te verbinden',
        'otp' => 'Eenmalig wachtwood',
        'password' => 'Wachtwoord',
        'password-confirmation' => 'Wachtwoord bevestigen',
        'reset-password' => 'Wachtwoord opnieuw instellen',
        'reset-send' => 'Link sturen om wachtwoord opnieuw in te stellen',
        'verify-login' => 'Login verifiëren',
    ],
    'buckets' => [
        'intro' => 'Wat wil je vandaag uitlichten?',
        'none-available' => 'Geen items beschikbaar',
        'none-featured' => 'Geen items uitgelicht.',
        'publish' => 'Publiceren',
        'source-title' => 'Beschikbare items',
    ],
    'dashboard' => [
        'all-activity' => 'Alle activiteit',
        'create-new' => 'Nieuw maken',
        'empty-message' => 'U heeft nog geen activiteiten.',
        'my-activity' => 'Mijn activiteit',
        'my-drafts' => 'Mijn concepten',
        'search-placeholder' => 'Zoek in alles...',
        'statitics' => 'Statistieken',
        'search' => [
            'loading' => 'Laden...',
            'no-result' => 'Geen resultaten gevonden',
            'last-edit' => 'Laatst aangepast',
        ],
        'activities' => [
            'created' => 'Aangemaakt',
            'updated' => 'Gewijzigd',
            'unpublished' => 'Gedepubliceerd',
            'published' => 'Gepubliceerd',
            'featured' => 'Gemarkeerd',
            'unfeatured' => 'Gedemarkeerd',
            'restored' => 'Hersteld',
            'deleted' => 'Verwijderd',
        ],
        'activity-row' => [
            'edit' => 'Wijzigen',
            'view-permalink' => 'Permalink bekijken',
            'by' => 'door',
        ],
        'unknown-author' => 'Ongekend',
    ],
    'dialog' => [
        'cancel' => 'Annuleren',
        'ok' => 'OK',
        'title' => 'Naar prullenbak verplaatsen',
    ],
    'editor' => [
        'cancel' => 'Annuleren',
        'delete' => 'Verwijderen',
        'done' => 'Klaar',
        'title' => 'Inhoud editor',
    ],
    'emails' => [
        'all-rights-reserved' => 'Alle rechten voorbehouden.',
        'hello' => 'Hallo!',
        'problems' => 'Als je problemen hebt met op de ":actionText" knop te drukken, kopieer en plak deze URL in je browser: [:url](:url)',
        'regards' => 'Met vriendelijke groeten,',
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
            'add-item' => 'Item toevoegen',
            'clone-block' => 'Blok dupliceren',
            'select-existing' => 'Bestaande selecteren',
        ],
        'browser' => [
            'add-label' => 'Toevoegen',
            'attach' => 'Bijvoegen',
        ],
        'files' => [
            'add-label' => 'Toevoegen',
        ],
        'generic' => [
            'switch-language' => 'Taal wijzigen',
        ],
        'map' => [
            'hide' => 'Kaart&nbsp;verbergen',
            'show' => 'Kaart&nbsp;tonen',
        ],
        'medias' => [
            'btn-label' => 'Afbeelding bijvoegen',
            'crop' => 'Bijsnijden',
            'crop-edit' => 'Bijsnijden van afbeelding aanpassen',
            'crop-list' => 'bijsnijden',
            'crop-save' => 'Bijwerken',
            'delete' => 'Verwijderen',
            'download' => 'Downloaden',
            'edit-close' => 'Informatie sluiten',
            'edit-info' => 'Informatie aanpassen',
            'original-dimensions' => 'Originele',
            'alt-text' => 'Alt tekst',
            'caption' => 'Bijschrijft',
            'video-url' => 'Video URL (optioneel)',
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
        'dialogs' => [
            'delete' => [
                'confirm' => 'Verwijderen',
                'confirmation' => 'Ben je zeker?<br/>Deze aanpassing kan niet ongedaan gemaakt worden',
                'delete-content' => 'Inhoud verwijderen',
                'title' => 'Inhoud verwijderen',
            ],
        ],
        'editor' => 'Editor',
    ],
    'lang-manager' => [
        'published' => 'Live',
    ],
    'lang-switcher' => [
        'edit-in' => 'Aanpassen in',
    ],
    'listing' => [
        'add-new-button' => 'Nieuw toevoegen',
        'bulk-actions' => 'Bulk acties',
        'bulk-clear' => 'Leegmaken',
        'columns' => [
            'featured' => 'Uitgelicht',
            'name' => 'Naam',
            'published' => 'Gepubliceerd',
            'show' => 'Tonen',
            'thumbnail' => 'Thumbnail',
        ],
        'dialogs' => [
            'delete' => [
                'confirm' => 'Verwijderen',
                'disclaimer' => 'Dit item wordt niet verwijderd, maar gaat naar de prullenbak',
                'move-to-trash' => 'Naar prullenbak',
                'title' => 'Item verwijderen',
            ],
            'destroy' => [
                'confirm' => 'Vernietigen',
                'destroy-permanently' => 'Permanent vernietigen',
                'disclaimer' => 'Dit item zal niet meer hersteld kunnen worden',
                'title' => 'Item vernietigen',
                'cancel' => 'Annuleren',
            ],
            'cancel' => 'Annuleren',
        ],
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
            'no' => 'Nee',
            'yes' => 'Ja',
            'all-items' => 'Alle items',
            'draft' => 'Concept',
            'mine' => 'Van mij',
            'published' => 'Gepubliceerd',
            'trash' => 'Prullenbak',
        ],
        'filters' => [
            'all-label' => 'Alle :label',
        ],
        'languages' => 'Talen',
        'listing-empty-message' => 'Er is nog geen item hier.',
        'paginate' => [
            'rows-per-page' => 'Rijen per pagina:',
        ],
        'bulk-selected-item' => 'item geselcteerd',
        'bulk-selected-items' => 'items geselecteerd',
        'reorder' => [
            'success' => ':modelTitle volgorde is aangepast!',
            'error' => ':modelTitle volgorde is niet aangepast. Er ging iets fout!',
        ],
        'restore' => [
            'success' => ':modelTitle hersteld!',
            'error' => ':modelTitle is niet hersteld. Er ging iets fout!',
        ],
        'bulk-restore' => [
            'success' => ':modelTitle items hersteld!',
            'error' => ':modelTitle items zijn niet hersteld. Er ging iets fout!',
        ],
        'force-delete' => [
            'success' => ':modelTitle vernietigd!',
            'error' => ':modelTitle is niet vernietigd. Er ging iets fout!',
        ],
        'bulk-force-delete' => [
            'success' => ':modelTitle items vernietigd!',
            'error' => ':modelTitle items zijn niet vernietigd. Er ging iets fout!',
        ],
        'delete' => [
            'success' => ':modelTitle naar de prullenbak verplaatst!',
            'error' => ':modelTitle is niet naar de prullenbak verplaatst. Er ging iets fout!',
        ],
        'bulk-delete' => [
            'success' => ':modelTitle items zijn naar de prullenbak verplaatst!',
            'error' => ':modelTitle items zijn niet naar de prullenbak verplaatst. Er ging iets fout!',
        ],
        'duplicate' => [
            'success' => ':modelTitle succesvol gedupliceerd!',
            'error' => ':modelTitle is niet gedupliceerd. Er ging iets fout!',
        ],
        'publish' => [
            'unpublished' => ':modelTitle gedepubliceerd!',
            'published' => ':modelTitle gepubliceerd!',
            'error' => ':modelTitle is niet gepubliceerd. Er ging iets fout!',
        ],
        'featured' => [
            'unfeatured' => ':modelTitle gedemarkeerd!',
            'featured' => ':modelTitle gemarkeerd!',
            'error' => ':modelTitle is niet gemarkeerd. Er ging iets fout!',
        ],
        'bulk-featured' => [
            'unfeatured' => ':modelTitle items gedemarkeerd!',
            'featured' => ':modelTitle items gemarkeerd!',
            'error' => ':modelTitle items zijn niet gemarkeerd. Er ging iets fout!',
        ],
        'bulk-publish' => [
            'unpublished' => ':modelTitle items gedepubliceerd!',
            'published' => ':modelTitle items gepubliceerd!',
            'error' => ':modelTitle items zijn niet gepubliceerd. Er ging iets fout!',
        ],
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
        'insert' => 'Invoegen',
        'sidebar' => [
            'alt-text' => 'Alt tekst',
            'caption' => 'Bijschrift',
            'clear' => 'Leegmaken',
            'dimensions' => 'Dimensies',
            'empty-text' => 'Geen bestand geselecteerd',
            'files-selected' => 'bestanden geselecteerd',
            'tags' => 'Tags',
        ],
        'title' => 'Mediabibliotheek',
        'update' => 'Bijwerken',
        'unused-filter-label' => 'Toon enkel ongebruikte',
        'no-tags-found' => 'Sorry, geen tags gevonden.',
        'dialogs' => [
            'delete' => [
                'delete-media-title' => 'Media verwijderen',
                'delete-media-desc' => 'Ben je zeker?<br />Deze aanpassing kan niet ongedaan gemaakt worden.',
                'delete-media-confirm' => 'Verwijderen',
                'title' => 'Ben je zeker?',
                'allow-delete-multiple-medias' => 'Sommige bestanden worden gebruikt en kunnen niet verwijderd worden. Wil je de andere verwijderen?',
                'allow-delete-one-media' => 'Dit bestand wordt gebruikt en kan niet verwijderd worden. Wil je de andere verwijderen?',
                'dont-allow-delete-multiple-medias' => 'Deze bestanden worden gebruikt en kunnen niet verwijderd worden.',
                'dont-allow-delete-one-media' => 'Dit bestand wordt gebruikt en kan niet verwijderd worden.',
            ],
            'replace' => [
                'replace-media-title' => 'Media vervangen',
                'replace-media-desc' => 'Ben je zeker?<br />Deze aanpassing kan niet ongedaan gemaakt worden.',
                'replace-media-confirm' => 'Vervangen',
            ],
        ],
        'types' => [
            'single' => [
                'image' => 'afbeelding',
                'video' => 'video',
                'file' => 'bestand',
            ],
            'multiple' => [
                'image' => 'afbeeldingen',
                'video' => 'video\'s',
                'file' => 'bestanden',
            ],
        ],
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
            'button' => 'Bijwerken',
            'title' => 'Bijwerken',
        ],
        'done' => [
            'button' => 'Klaar',
        ],
    ],
    'nav' => [
        'admin' => 'Admin',
        'cms-users' => 'CMS-gebruikers',
        'logout' => 'Uitloggen',
        'media-library' => 'Mediabibliotheek',
        'settings' => 'Instellingen',
        'close-menu' => 'Menu sluiten',
        'profile' => 'Profiel',
        'open-live-site' => 'Live site openen',
    ],
    'notifications' => [
        'reset' => [
            'action' => 'Wachtwoord opnieuw instellen',
            'content' => 'Je ontvangt deze e-mail omdat we een nieuw wachtwoord vraag kregen. Als je niet aangevraagd hebt om je wachtwoord opnieuw in te stellen, hoef je niks te doen.',
            'subject' => ':appName | Wachtwoord resetten',
        ],
        'welcome' => [
            'action' => 'Kies je eigen wachtwoord',
            'content' => 'Je ontvangt deze e-mail omdat er een account is aangemaakt voor jou op :name.',
            'title' => 'Welkom',
            'subject' => ':appName | Welkom',
        ],
    ],
    'overlay' => [
        'close' => 'Sluiten',
    ],
    'previewer' => [
        'compare-view' => 'Vergelijken',
        'current-revision' => 'Huidige',
        'editor' => 'Editor',
        'last-edit' => 'Laatst aangepast',
        'past-revision' => 'Voorgaande',
        'restore' => 'Herstellen',
        'revision-history' => 'Geschiedenis',
        'single-view' => 'Enkele weergave',
        'title' => 'Voorbeeld van aanpassingen bekijken',
        'unsaved' => 'Voorbeeld van je niet opgeslagen aanpassingen aan het bekijken',
        'drag-and-drop' => 'Sleep inhoud van de linker navigatie',
    ],
    'publisher' => [
        'cancel' => 'Annuleren',
        'current' => 'Huidige',
        'end-date' => 'Einddatum',
        'immediate' => 'Onmiddellijk',
        'languages' => 'Talen',
        'languages-published' => 'Live',
        'last-edit' => 'Laatst aangepast',
        'preview' => 'Voorbeeld van aanpassingen bekijken',
        'publish' => 'Publiceren',
        'publish-close' => 'Publiceren en sluiten',
        'publish-new' => 'Publiceren en nieuwe aanmaken',
        'published-on' => 'Gepubliceerd op',
        'restore-draft' => 'Herstellen als concept',
        'restore-draft-close' => 'Herstellen als concept en sluiten',
        'restore-draft-new' => 'Herstellen als concept en nieuwe aanmaken',
        'restore-live' => 'Herstellen als gepubliceerd',
        'restore-live-close' => 'Herstellen als gepubliceerd en sluiten',
        'restore-live-new' => 'Herstellen als gepubliceerd en nieuwe aanmaken',
        'restore-message' => 'Je bent momenteel een oudere herziening van deze inhoud aan het bekijken (opgeslaan door :user op :date). Maak aanpassingen indien nodig en klik op herstellen om een nieuwe herziening op te slaan.',
        'restore-success' => 'Herziening hersteld.',
        'revisions' => 'Herzieningen',
        'save' => 'Opslaan als concept',
        'save-close' => 'Opslaan als concept en sluiten',
        'save-new' => 'Opslaan als concept en nieuwe aanmaken',
        'save-success' => 'Inhoud opgeslaan.',
        'start-date' => 'Startdatum',
        'switcher-title' => 'Status',
        'update' => 'Bijwerken',
        'update-close' => 'Bijwerken en sluiten',
        'update-new' => 'Bijwerken en nieuwe aanmaken',
        'parent-page' => 'Bovenliggende pagina',
        'review-status' => 'Status herzien',
        'visibility' => 'Zichtbaarheid',
        'scheduled' => 'Gepland',
        'expired' => 'Afgelopen',
        'unsaved-changes' => 'Er zijn niet opgeslagen aanpassingen',
    ],
    'select' => [
        'empty-text' => 'Sorry, geen overeenkomstige opties.',
    ],
    'uploader' => [
        'dropzone-text' => 'of sleep nieuwe bestanden naar hier',
        'upload-btn-label' => 'Nieuw toevoegen',
    ],
    'user-management' => [
        '2fa' => '2-factor authenticatie',
        '2fa-description' => 'Scan deze QR code met een Google Authenticator compatibele app en voer je eenmalig wachtwoord in. Een lijst van compatibele apps vind je <a href=":link" target="_blank" rel="noopener">hier</a>.',
        '2fa-disable' => 'Voer je eenmalig wachtwoord in om de 2-factor authenticatie uit te schakelen',
        'active' => 'Actief',
        'cancel' => 'Annuleren',
        'content-fieldset-label' => 'Gebruikersinstellingen',
        'description' => 'Omschrijving',
        'disabled' => 'Uitgeschakeld',
        'edit-modal-title' => 'Bewerk gebruikersnaam',
        'email' => 'E-mail',
        'enable-user' => 'Gebruiker inschakelen',
        'enable-user-and-close' => 'Gebruiker inschakelen en sluiten',
        'enable-user-and-create-new' => 'Gebruiker inschakelen en nieuwe aanmaken',
        'enabled' => 'Ingeschakeld',
        'language' => 'Taal',
        'language-placeholder' => 'Taal selecteren',
        'name' => 'Naam',
        'otp' => 'Eenmalig wachtwoord',
        'profile-image' => 'Profielfoto',
        'role' => 'Rol',
        'role-placeholder' => 'Rol selecteren',
        'title' => 'Titel',
        'trash' => 'Prullenbak',
        'update' => 'Bijwerken',
        'update-and-close' => 'Bijwerken en sluiten',
        'update-and-create-new' => 'Bijwerken en nieuwe aanmaken',
        'update-disabled-and-close' => 'Uitgeschakeld bijwerken en sluiten',
        'update-disabled-user' => 'Uitgeschakelde gebruiker bijwerken',
        'update-disabled-user-and-create-new' => 'Uitgeschakelde gebruiker bijwerken en nieuwe aanmaken',
        'user-image' => 'Afbeelding',
        'users' => 'Gebruikers',
        'force-2fa-disable' => '2FA uitschakelen',
        'force-2fa-disable-description' => 'Tekst in het veld typen om 2FA voor deze gebruiker uit te schakelen',
        'force-2fa-disable-challenge' => '2FA voor :user uitschakelen',
        'pending' => 'Aan het afwachten',
        'activation-pending' => 'Op activatie aan het wachten',
    ],
    'settings' => [
        'update' => 'Bijwerken',
        'cancel' => 'Annuleren',
        'fieldset-label' => 'Instellingen aanpassen',
    ],
    'permissions' => [
        'groups' => [
            'title' => 'Groepen',
            'published' => 'Aan',
            'draft' => 'Uit',
        ],
        'roles' => [
            'title' => 'Rollen',
            'published' => 'Aan',
            'draft' => 'Uit',
        ],
    ],
];
