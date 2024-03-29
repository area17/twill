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
        'back-to-login' => 'Nazaj na prijavo',
        'choose-password' => 'Izberi geslo',
        'email' => 'E-pošta',
        'forgot-password' => 'Pozabljeno geslo',
        'login' => 'Prijava',
        'login-title' => 'Prijava',
        'oauth-link-title' => 'Ponovno vnesite svoje geslo za povezavo :provider z vašim računom',
        'otp' => 'Enkratno geslo',
        'password' => 'Geslo',
        'password-confirmation' => 'Potrdi geslo',
        'reset-password' => 'Ponastavi geslo',
        'reset-send' => 'Pošlji povezavo za ponastavitev gesla',
        'verify-login' => 'Potrdi prijavo',
        'auth-causer' => 'Avtentikacija',
    ],
    'buckets' => [
        'intro' => 'Kaj bi radi izpostavili danes?',
        'none-available' => 'Ni razpoložljivih predmetov.',
        'none-featured' => 'Ni izpostavljenih predmetov.',
        'publish' => 'Objavi',
        'source-title' => 'Razpoložljivi predmeti',
    ],
    'dashboard' => [
        'all-activity' => 'Vsa dejavnost',
        'create-new' => 'Ustvari novo',
        'empty-message' => 'Brez dejavnosti',
        'my-activity' => 'Moja dejavnost',
        'my-drafts' => 'Moji osnutki',
        'search-placeholder' => 'Išči vse ...',
        'statitics' => 'Statistika',
        'search' => [
            'loading' => 'Nalaganje…',
            'no-result' => 'Ni najdenih rezultatov.',
            'last-edit' => 'Nazadnje urejeno',
        ],
        'activities' => [
            'created' => 'Ustvarjeno',
            'updated' => 'Posodobljeno',
            'unpublished' => 'Neobjavljeno',
            'published' => 'Objavljeno',
            'featured' => 'Izpostavljeno',
            'unfeatured' => 'Neizpostavljeno',
            'restored' => 'Obnovljeno',
            'deleted' => 'Izbrisano',
            'login' => 'Akcija prijave',
            'logout' => 'Akcija odjave',
        ],
        'activity-row' => [
            'edit' => 'Uredi',
            'view-permalink' => 'Ogled stalne povezave',
            'by' => 'od',
        ],
        'unknown-author' => 'Neznano',
    ],
    'dialog' => [
        'cancel' => 'Prekliči',
        'ok' => 'V redu',
        'title' => 'Premakni v smeti',
    ],
    'editor' => [
        'cancel' => 'Prekliči',
        'delete' => 'Izbriši',
        'done' => 'Končano',
        'title' => 'Urejevalnik vsebine',
    ],
    'emails' => [
        'all-rights-reserved' => 'Vse pravice pridržane.',
        'hello' => 'Pozdravljeni!',
        'problems' => 'Če imate težave s klikom na gumb ":actionText", kopirajte in prilepite spodnji URL v vaš spletni brskalnik: [:url](:url)',
        'regards' => 'Lep pozdrav,',
    ],
    'fields' => [
        'block-editor' => [
            'add-content' => 'Dodaj vsebino',
            'collapse-all' => 'Zmanjšaj vse',
            'create-another' => 'Ustvari še eno',
            'delete' => 'Izbriši',
            'expand-all' => 'Razširi vse',
            'loading' => 'Nalaganje',
            'open-in-editor' => 'Odpri v urejevalniku',
            'preview' => 'Predogled',
            'add-item' => 'Dodaj element',
            'clone-block' => 'Kloniraj blok',
            'select-existing' => 'Izberi obstoječe',
        ],
        'browser' => [
            'add-label' => 'Dodaj',
            'attach' => 'Priloži',
        ],
        'files' => [
            'add-label' => 'Dodaj',
        ],
        'generic' => [
            'switch-language' => 'Preklopi jezik',
        ],
        'map' => [
            'hide' => 'Skrij&nbsp;zemljevid',
            'show' => 'Pokaži&nbsp;zemljevid',
        ],
        'medias' => [
            'btn-label' => 'Priloži sliko',
            'crop' => 'Obreži',
            'crop-edit' => 'Uredi obrez slik',
            'crop-list' => 'obrez',
            'crop-save' => 'Posodobi',
            'delete' => 'Izbriši',
            'download' => 'Prenesi',
            'edit-close' => 'Zapri informacije',
            'edit-info' => 'Uredi informacije',
            'original-dimensions' => 'Izvirnik',
            'alt-text' => 'Alt besedilo',
            'caption' => 'Napis',
            'video-url' => 'URL videoposnetka (neobvezno)',
        ],
    ],
    'filter' => [
        'apply-btn' => 'Uporabi',
        'clear-btn' => 'Počisti',
        'search-placeholder' => 'Išči',
        'toggle-label' => 'Filtriraj',
    ],
    'footer' => [
        'version' => 'Različica',
    ],
    'form' => [
        'content' => 'Vsebina',
        'dialogs' => [
            'delete' => [
                'confirm' => 'Izbriši',
                'confirmation' => 'Ste prepričani?<br />Te spremembe ni mogoče razveljaviti.',
                'delete-content' => 'Izbriši vsebino',
                'title' => 'Izbriši vsebino',
            ],
        ],
        'editor' => 'Urednik',
        'options' => 'Možnosti',
    ],
    'lang-manager' => [
        'published' => 'V živo',
    ],
    'lang-switcher' => [
        'edit-in' => 'Uredi v',
    ],
    'listing' => [
        'add-new-button' => 'Dodaj novo',
        'bulk-actions' => 'Množične akcije',
        'bulk-clear' => 'Počisti',
        'columns' => [
            'featured' => 'Izpostavljeno',
            'name' => 'Ime',
            'published' => 'Objavljeno',
            'show' => 'Pokaži',
            'thumbnail' => 'Sličica',
        ],
        'dialogs' => [
            'delete' => [
                'confirm' => 'Izbriši',
                'disclaimer' => 'Predmet ne bo izbrisan, ampak premaknjen v smeti.',
                'move-to-trash' => 'Premakni v smeti',
                'title' => 'Izbriši predmet',
            ],
            'destroy' => [
                'confirm' => 'Uniči',
                'destroy-permanently' => 'Trajno uniči',
                'disclaimer' => 'Predmet ne bo več mogoče obnoviti.',
                'title' => 'Uniči predmet',
            ],
        ],
        'dropdown' => [
            'delete' => 'Izbriši',
            'destroy' => 'Uniči',
            'duplicate' => 'Podvoji',
            'edit' => 'Uredi',
            'publish' => 'Objavi',
            'feature' => 'Izpostavi',
            'restore' => 'Obnovi',
            'unfeature' => 'Ne izpostavi',
            'unpublish' => 'Prekliči objavo',
        ],
        'filter' => [
            'no' => 'Ne',
            'yes' => 'Da',
            'all-items' => 'Vsi predmeti',
            'draft' => 'Osnutek',
            'mine' => 'Moje',
            'published' => 'Objavljeno',
            'trash' => 'Smeti',
            'not-set' => 'Brez vrednosti',
        ],
        'filters' => [
            'all-label' => 'Vse :label',
        ],
        'languages' => 'Jeziki',
        'listing-empty-message' => 'Tukaj še ni predmeta.',
        'paginate' => [
            'rows-per-page' => 'Vrstic na stran:',
        ],
        'bulk-selected-item' => 'izbran element',
        'bulk-selected-items' => 'izbrani elementi',
        'reorder' => [
            'success' => ':modelTitle vrstni red spremenjen!',
            'error' => ':modelTitle vrstni red ni bil spremenjen. Nekaj je šlo narobe!',
        ],
        'restore' => [
            'success' => ':modelTitle obnovljen!',
            'error' => ':modelTitle ni bil obnovljen. Nekaj je šlo narobe!',
        ],
        'bulk-restore' => [
            'success' => ':modelTitle elementi obnovljeni!',
            'error' => ':modelTitle elementi niso bili obnovljeni. Nekaj je šlo narobe!',
        ],
        'force-delete' => [
            'success' => ':modelTitle uničen!',
            'error' => ':modelTitle ni bil uničen. Nekaj je šlo narobe!',
        ],
        'bulk-force-delete' => [
            'success' => ':modelTitle elementi uničeni!',
            'error' => ':modelTitle elementi niso bili uničeni. Nekaj je šlo narobe!',
        ],
        'delete' => [
            'success' => ':modelTitle premaknjen v smeti!',
            'error' => ':modelTitle ni bil premaknjen v smeti. Nekaj je šlo narobe!',
        ],
        'bulk-delete' => [
            'success' => ':modelTitle elementi premaknjeni v smeti!',
            'error' => ':modelTitle elementi niso bili premaknjeni v smeti. Nekaj je šlo narobe!',
        ],
        'duplicate' => [
            'success' => ':modelTitle uspešno podvojen!',
            'error' => ':modelTitle ni bil podvojen. Nekaj je šlo narobe!',
        ],
        'publish' => [
            'unpublished' => ':modelTitle neobjavljen!',
            'published' => ':modelTitle objavljen!',
            'error' => ':modelTitle ni bil objavljen. Nekaj je šlo narobe!',
        ],
        'featured' => [
            'unfeatured' => ':modelTitle neizpostavljen!',
            'featured' => ':modelTitle izpostavljen!',
            'error' => ':modelTitle ni bil izpostavljen. Nekaj je šlo narobe!',
        ],
        'bulk-featured' => [
            'unfeatured' => ':modelTitle elementi neizpostavljeni!',
            'featured' => ':modelTitle elementi izpostavljeni!',
            'error' => ':modelTitle elementi niso bili izpostavljeni. Nekaj je šlo narobe!',
        ],
        'bulk-publish' => [
            'unpublished' => ':modelTitle elementi neobjavljeni!',
            'published' => ':modelTitle elementi objavljeni!',
            'error' => ':modelTitle elementi niso bili objavljeni. Nekaj je šlo narobe!',
        ],
    ],
    'main' => [
        'create' => 'Ustvari',
        'draft' => 'Osnutek',
        'published' => 'V živo',
        'title' => 'Naslov',
        'update' => 'Posodobi',
    ],
    'media-library' => [
        'files' => 'Datoteke',
        'filter-select-label' => 'Filtriraj po oznaki',
        'images' => 'Slike',
        'insert' => 'Vstavi',
        'sidebar' => [
            'alt-text' => 'Alt besedilo',
            'caption' => 'Napis',
            'clear' => 'Počisti',
            'dimensions' => 'Dimenzije',
            'empty-text' => 'Ni izbrane datoteke',
            'files-selected' => 'izbranih datotek',
            'tags' => 'Oznake',
        ],
        'title' => 'Medijska knjižnica',
        'update' => 'Posodobi',
        'unused-filter-label' => 'Pokaži samo neuporabljeno',
        'no-tags-found' => 'Žal, oznak ni bilo mogoče najti.',
        'dialogs' => [
            'delete' => [
                'delete-media-title' => 'Izbriši medij',
                'delete-media-desc' => 'Ali ste prepričani?<br />Te spremembe ni mogoče razveljaviti.',
                'delete-media-confirm' => 'Izbriši',
                'title' => 'Ali ste prepričani?',
                'allow-delete-multiple-medias' => 'Nekatere datoteke se uporabljajo in jih ni mogoče izbrisati. Želite izbrisati druge?',
                'allow-delete-one-media' => 'Ta datoteka se uporablja in je ni mogoče izbrisati. Želite izbrisati druge?',
                'dont-allow-delete-multiple-medias' => 'Te datoteke se uporabljajo in jih ni mogoče izbrisati.',
                'dont-allow-delete-one-media' => 'Ta datoteka se uporablja in je ni mogoče izbrisati.',
            ],
            'replace' => [
                'replace-media-title' => 'Zamenjaj medij',
                'replace-media-desc' => 'Ali ste prepričani?<br />Te spremembe ni mogoče razveljaviti.',
                'replace-media-confirm' => 'Zamenjaj',
            ],
        ],
        'types' => [
            'single' => [
                'image' => 'slika',
                'video' => 'video',
                'file' => 'datoteka',
            ],
            'multiple' => [
                'image' => 'slike',
                'video' => 'videoposnetki',
                'file' => 'datoteke',
            ],
        ],
    ],
    'modal' => [
        'create' => [
            'button' => 'Ustvari',
            'create-another' => 'Ustvari in dodaj še enega',
            'title' => 'Dodaj novo',
        ],
        'permalink-field' => 'Permalink',
        'title-field' => 'Naslov',
        'update' => [
            'button' => 'Posodobi',
            'title' => 'Posodobi',
        ],
        'done' => [
            'button' => 'Končano',
        ],
    ],
    'nav' => [
        'admin' => 'Admin',
        'cms-users' => 'Uporabniki CMS',
        'logout' => 'Odjava',
        'media-library' => 'Medijska knjižnica',
        'settings' => 'Nastavitve',
        'close-menu' => 'Zapri meni',
        'profile' => 'Profil',
        'open-live-site' => 'Odpri v živo stran',
    ],
    'notifications' => [
        'reset' => [
            'action' => 'Ponastavi geslo',
            'content' => 'Prejeli ste to e-pošto, ker smo prejeli zahtevo za ponastavitev gesla. Če niste zahtevali ponastavitve gesla, ni potrebno nadaljnjih ukrepov.',
            'subject' => ':appName | Ponastavi geslo',
        ],
        'welcome' => [
            'action' => 'Izberite svoje geslo',
            'content' => 'Prejeli ste to e-pošto, ker je bil za vas ustvarjen račun na :name.',
            'title' => 'Dobrodošli',
            'subject' => ':appName | Dobrodošli',
        ],
    ],
    'overlay' => [
        'close' => 'Zapri',
    ],
    'previewer' => [
        'compare-view' => 'Primerjaj pogled',
        'current-revision' => 'Trenutno',
        'editor' => 'Urednik',
        'last-edit' => 'Nazadnje urejeno',
        'past-revision' => 'Preteklo',
        'restore' => 'Obnovi',
        'revision-history' => 'Zgodovina revizij',
        'single-view' => 'Enojni pogled',
        'title' => 'Predogled sprememb',
        'unsaved' => 'Predogled z vašimi neshranjenimi spremembami',
        'drag-and-drop' => 'Vsebine povleci in spusti iz levega menija',
    ],
    'publisher' => [
        'cancel' => 'Prekliči',
        'current' => 'Trenutno',
        'end-date' => 'Datum zaključka',
        'immediate' => 'Takoj',
        'languages' => 'Jeziki',
        'languages-published' => 'V živo',
        'last-edit' => 'Nazadnje urejeno',
        'preview' => 'Predogled sprememb',
        'publish' => 'Objavi',
        'publish-close' => 'Objavi in zapri',
        'publish-new' => 'Objavi in ustvari novo',
        'published-on' => 'Objavljeno dne',
        'restore-draft' => 'Obnovi kot osnutek',
        'restore-draft-close' => 'Obnovi kot osnutek in zapri',
        'restore-draft-new' => 'Obnovi kot osnutek in ustvari novo',
        'restore-live' => 'Obnovi kot objavljeno',
        'restore-live-close' => 'Obnovi kot objavljeno in zapri',
        'restore-live-new' => 'Obnovi kot objavljeno in ustvari novo',
        'restore-message' => 'Trenutno urejate starejšo revizijo te vsebine (shranil :user na :date). Po potrebi naredite spremembe in kliknite obnovi za shranjevanje nove revizije.',
        'restore-success' => 'Revizija obnovljena.',
        'revisions' => 'Revizije',
        'save' => 'Shrani kot osnutek',
        'save-close' => 'Shrani kot osnutek in zapri',
        'save-new' => 'Shrani kot osnutek in ustvari novo',
        'save-success' => 'Vsebina shranjena. Vse je v redu!',
        'start-date' => 'Datum začetka',
        'switcher-title' => 'Stanje',
        'update' => 'Posodobi',
        'update-close' => 'Posodobi in zapri',
        'update-new' => 'Posodobi in ustvari novo',
        'parent-page' => 'Nadrejena stran',
        'review-status' => 'Stanje pregleda',
        'visibility' => 'Vidnost',
        'scheduled' => 'Načrtovano',
        'expired' => 'Potečeno',
        'unsaved-changes' => 'Obstajajo neshranjene spremembe',
        'draft-revision' => 'Shrani kot osnutek revizije',
        'draft-revision-close' => 'Shrani kot osnutek revizije in zapri',
        'draft-revision-new' => 'Shrani kot osnutek revizije in ustvari novo',
        'draft-revisions-available' => 'Trenutno si ogledujete objavljeno različico te vsebine. Na voljo so novejše osnutkovske revizije.',
        'editing-draft-revision' => 'Trenutno urejate osnutek revizije te vsebine. Po potrebi naredite spremembe in kliknite Shrani kot revizijo ali Objavi.',
    ],
    'select' => [
        'empty-text' => 'Žal, ujemajočih se možnosti ni.',
    ],
    'uploader' => [
        'dropzone-text' => 'ali spustite nove datoteke tukaj',
        'upload-btn-label' => 'Dodaj novo',
    ],
    'user-management' => [
        '2fa' => 'Dvofaktorska avtentikacija',
        '2fa-description' => 'Prosimo, skenirajte to QR kodo z aplikacijo, ki je združljiva z Google Authenticatorjem, in pred oddajo vnesite svoje enkratno geslo spodaj. Seznam združljivih aplikacij najdete <a href=":link" target="_blank" rel="noopener">tukaj</a>.',
        '2fa-disable' => 'Vnesite svoje enkratno geslo za onemogočanje dvofaktorske avtentikacije',
        'active' => 'Aktivno',
        'cancel' => 'Prekliči',
        'content-fieldset-label' => 'Račun',
        'description' => 'Opis',
        'disabled' => 'Onemogočeno',
        'edit-modal-title' => 'Uredi uporabniško ime',
        'email' => 'E-pošta',
        'enable-user' => 'Omogoči uporabnika',
        'enable-user-and-close' => 'Omogoči uporabnika in zapri',
        'enable-user-and-create-new' => 'Omogoči uporabnika in ustvari novo',
        'enabled' => 'Omogočeno',
        'language' => 'Jezik',
        'language-placeholder' => 'Izberi jezik',
        'name' => 'Ime',
        'otp' => 'Enkratno geslo',
        'profile-image' => 'Profilna slika',
        'role' => 'Vloga',
        'role-placeholder' => 'Izberi vlogo',
        'title' => 'Naziv',
        'trash' => 'Koš',
        'update' => 'Posodobi',
        'update-and-close' => 'Posodobi in zapri',
        'update-and-create-new' => 'Posodobi in ustvari novo',
        'update-disabled-and-close' => 'Posodobi onemogočeno in zapri',
        'update-disabled-user' => 'Posodobi onemogočenega uporabnika',
        'update-disabled-user-and-create-new' => 'Posodobi onemogočenega uporabnika in ustvari novo',
        'user-image' => 'Slika',
        'users' => 'Uporabniki',
        'force-2fa-disable' => 'Onemogoči 2FA',
        'force-2fa-disable-description' => 'Vnesite besedilo, prikazano v polju, da onemogočite 2FA za tega uporabnika',
        'force-2fa-disable-challenge' => 'Onemogoči 2FA za :user',
        'pending' => 'V teku',
        'activation-pending' => 'Čakanje na aktivacijo',
    ],
    'settings' => [
        'update' => 'Posodobi',
        'cancel' => 'Prekliči',
        'fieldset-label' => 'Uredi nastavitve',
    ],
    'permissions' => [
        'groups' => [
            'title' => 'Skupine',
            'published' => 'Omogočeno',
            'draft' => 'Onemogočeno',
        ],
        'roles' => [
            'title' => 'Vloge',
            'published' => 'Omogočeno',
            'draft' => 'Onemogočeno',
        ],
    ],
];
