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
        'forgot-password' => 'Mot de passe oublié',
        'login' => 'Se connecter',
        'login-title' => 'Connexion',
        'password' => 'Mot de passe',
    ],
    'dashboard' => [
        'all-activity' => 'Toute l’activité',
        'create-new' => 'Créer',
        'empty-message' => 'Vous n’avez aucune activité pour le moment.',
        'my-activity' => 'Mon activité',
        'my-drafts' => 'Mes brouillons',
        'search-placeholder' => 'Rechercher...',
        'statitics' => 'Statistiques',
    ],
    'fields' => [
        'block-editor' => [
            'add-content' => 'Ajouter du contenu',
            'collapse-all' => 'Tout masquer',
            'create-another' => 'Créer un nouveau',
            'delete' => 'Supprimer',
            'expend-all' => 'Tout afficher',
            'loading' => 'Chargement',
            'open-in-editor' => 'Ouvrir dans l’éditeur',
            'preview' => 'Prévisualiser',
        ],
        'browser' => [
            'add-label' => 'Ajouter des',
            'attach' => 'Ajouter des',
        ],
        'files' => [
            'add-label' => 'Ajouter',
        ],
        'medias' => [
            'btn-label' => 'Ajouter une image',
        ],
    ],
    'filter' => [
        'apply-btn' => 'Appliquer',
        'clear-btn' => 'Annuler',
        'search-placeholder' => 'Recherche',
        'toggle-label' => 'Filtrer',
    ],
    'footer' => [
        'version' => 'Version',
    ],
    'form' => [
        'content' => 'Contenu',
        'editor' => 'Éditeur',
    ],
    'listing' => [
        'add-new-button' => 'Ajouter',
        'dropdown' => [
            'delete' => 'Supprimer',
            'destroy' => 'Détruire',
            'duplicate' => 'Dupliquer',
            'edit' => 'Modifier',
            'feature' => 'Mettre en avant',
            'publish' => 'Publier',
            'restore' => 'Restaurer',
            'unfeature' => 'Ne plus mettre en avant',
            'unpublish' => 'Dépublier',
        ],
        'filter' => [
            'all-items' => 'Tous',
            'draft' => 'Brouillons',
            'mine' => 'Mes contenus',
            'published' => 'Publié',
            'trash' => 'Corbeille',
        ],
        'languages' => 'Langues',
        'listing-empty-message' => 'Il n\'y a aucun contenu pour le moment.',
    ],
    'main' => [
        'create' => 'Créer',
        'draft' => 'Brouillon',
        'published' => 'Publié',
        'title' => 'Titre',
        'update' => 'Mettre à jour',
    ],
    'media-library' => [
        'files' => 'Fichiers',
        'filter-select-label' => 'Filtrer par tag',
        'images' => 'Images',
        'sidebar' => [
            'clear' => 'Annuler',
            'dimensions' => 'Dimensions',
            'empty-text' => 'Aucun fichier sélectionnés',
            'files-selected' => 'fichiers sélectionnés',
        ],
        'title' => 'Media Library',
    ],
    'modal' => [
        'create' => [
            'button' => 'Créer',
            'create-another' => 'Créer et ajouter un(e) autre',
            'title' => 'Ajouter',
        ],
        'permalink-field' => 'Permalien',
        'title-field' => 'Titre',
        'update' => [
            'title' => 'Mettre à jour',
        ],
    ],
    'nav' => [
        'admin' => 'Admin',
        'cms-users' => 'Utilisateurs du CMS',
        'logout' => 'Déconnexion',
        'media-library' => 'Media Library',
        'settings' => 'Paramètres',
    ],
    'publisher' => [
        'cancel' => 'Annuler',
        'publish' => 'Publier',
        'publish-close' => 'Publier et fermer',
        'publish-new' => 'Publier et créer un nouveau',
        'save' => 'Enregistrer comme brouillon',
        'save-close' => 'Enregistrer comme brouillon et fermer',
        'save-new' => 'Enregistrer comme brouillon et créer un nouveau',
        'switcher-title' => 'Status',
        'update' => 'Mettre à jour',
        'update-close' => 'Mettre à jour et fermer',
        'update-new' => 'Mettre à jour et créer un nouveau',
    ],
    'select' => [
        'empty-text' => 'Désolé, aucune correspondance trouvée.',
    ],
    'uploader' => [
        'dropzone-text' => 'ou faites glisser de nouveaux fichiers ici',
        'upload-btn-label' => 'Ajouter',
    ],
    'user-management' => [
        'active' => 'Actif',
        'cancel' => 'Annuler',
        'content-fieldset-label' => 'Paramètre utilisateur',
        'disabled' => 'Désactivé',
        'edit-modal-title' => 'Éditer le nom d’utilisateur',
        'email' => 'Email',
        'enable-user' => 'Activer l’utilisateur',
        'enable-user-and-close' => 'Activer l’utilisateur et fermer',
        'enable-user-and-create-new' => 'Activer l’utilisateur et créer un nouveau',
        'enabled' => 'Activé',
        'name' => 'Nom',
        'role' => 'Rôle',
        'trash' => 'Corbeille',
        'update' => 'Mettre à jour',
        'update-and-close' => 'Mettre à jour et fermer',
        'update-and-create-new' => 'Mettre à jour et créer un nouveau',
        'update-disabled-and-close' => 'Mettre à jour l’utilisateur désactivé et fermer',
        'update-disabled-user' => 'Mettre à jour l’utilisateur désactivé',
        'update-disabled-user-and-create-new' => 'Mettre à jour l’utilisateur désactivé et créer un nouveau',
        'user-image' => 'Image',
        'users' => 'Utilisateurs',
    ],
];
