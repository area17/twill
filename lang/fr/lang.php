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
        'back-to-login' => 'Se connecter',
        'choose-password' => 'Choisir un mot de passe',
        'email' => 'Email',
        'forgot-password' => 'Mot de passe oublié',
        'login' => 'Se connecter',
        'login-title' => 'Connexion',
        'oauth-link-title' => 'Entrez à nouveau votre mot de passe pour lier :provider à votre compte',
        'otp' => 'Mot de passe à usage unique',
        'password' => 'Mot de passe',
        'password-confirmation' => 'Confirmer le mot de passe',
        'reset-password' => 'Réinitialisation du mot de passe',
        'reset-send' => 'Envoyer un lien de réinitialisation de mot de passe',
        'verify-login' => 'Vérifier la connexion',
        'auth-causer' => 'Authentification',
    ],
    'buckets' => [
        'intro' => 'Que souhaitez-vous mettre en avant aujourd’hui ?',
        'none-available' => 'Aucun contenu disponible',
        'none-featured' => 'Aucun contenu mis en avant',
        'publish' => 'Publier',
        'source-title' => 'Contenu disponible',
    ],
    'dashboard' => [
        'all-activity' => 'Toute l’activité',
        'create-new' => 'Créer',
        'empty-message' => 'Vous n’avez aucune activité pour le moment.',
        'my-activity' => 'Mon activité',
        'my-drafts' => 'Mes brouillons',
        'search-placeholder' => 'Rechercher...',
        'statitics' => 'Statistiques',
        'search' => [
            'loading' => 'Chargement...',
            'no-result' => 'Aucun résultat trouvé.',
            'last-edit' => 'Édité',
        ],
        'activities' => [
            'created' => 'Créé',
            'updated' => 'Mis à jour',
            'unpublished' => 'Dépublié',
            'published' => 'Publié',
            'featured' => 'Mis en avant',
            'unfeatured' => 'N’est plus mis en avant',
            'restored' => 'Restauré',
            'deleted' => 'Supprimé',
            'login' => 'Connexion',
            'logout' => 'Déconnexion',
        ],
        'activity-row' => [
            'edit' => 'Éditer',
            'view-permalink' => 'Voir le permalien',
            'by' => 'par',
        ],
        'unknown-author' => 'Inconnu',
    ],
    'dialog' => [
        'cancel' => 'Annuler',
        'ok' => 'OK',
        'title' => 'Mettre à la corbeille',
    ],
    'editor' => [
        'cancel' => 'Annuler',
        'delete' => 'Supprimer',
        'done' => 'Fermer',
        'title' => 'Éditeur de contenu',
    ],
    'emails' => [
        'all-rights-reserved' => 'Tous droits réservés',
        'hello' => 'Bonjour !',
        'problems' => 'Si vous ne parvenez pas à cliquer sur le bouton ":actionText", copiez et collez l’URL ci-dessous dans votre navigateur Web : [:url] (:url)',
        'regards' => 'Cordialement,',
    ],
    'fields' => [
        'block-editor' => [
            'add-content' => 'Ajouter du contenu',
            'collapse-all' => 'Tout masquer',
            'create-another' => 'Créer un nouveau',
            'delete' => 'Supprimer',
            'expand-all' => 'Tout afficher',
            'loading' => 'Chargement',
            'open-in-editor' => 'Ouvrir dans l’éditeur',
            'preview' => 'Prévisualiser',
            'add-item' => 'Ajouter',
            'clone-block' => 'Dupliquer le bloc',
            'select-existing' => 'Sélectionner un élément existant',
        ],
        'browser' => [
            'add-label' => 'Ajouter des',
            'attach' => 'Ajouter des',
        ],
        'files' => [
            'add-label' => 'Ajouter',
        ],
        'generic' => [
            'switch-language' => 'Changer de langue',
        ],
        'map' => [
            'hide' => 'Masquer',
            'show' => 'Afficher',
        ],
        'medias' => [
            'btn-label' => 'Ajouter une image',
            'crop' => 'Recadrer',
            'crop-edit' => 'Éditer le crop de l’image',
            'crop-list' => 'crop',
            'crop-save' => 'Mettre à jour',
            'delete' => 'Supprimer',
            'download' => 'Télécharger',
            'edit-close' => 'Fermer infos',
            'edit-info' => 'Éditer infos',
            'original-dimensions' => 'Original',
            'alt-text' => 'Texte alternatif',
            'caption' => 'Légende',
            'video-url' => 'URL vidéo (optionnel)',
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
        'dialogs' => [
            'delete' => [
                'confirm' => 'Supprimer',
                'confirmation' => 'Êtes-vous sûr ? Ce changement ne peut être annulé.',
                'delete-content' => 'Supprimer le contenu',
                'title' => 'Supprimer le contenu',
            ],
        ],
        'editor' => 'Éditeur',
        'options' => 'Options',
    ],
    'lang-manager' => [
        'published' => 'Publié',
    ],
    'lang-switcher' => [
        'edit-in' => 'Éditer en',
    ],
    'listing' => [
        'add-new-button' => 'Ajouter',
        'bulk-actions' => 'Actions groupées',
        'bulk-clear' => 'Annuler',
        'columns' => [
            'featured' => 'Mis en avant',
            'name' => 'Nom',
            'published' => 'Publié',
            'show' => 'Afficher',
            'thumbnail' => 'Miniature',
        ],
        'dialogs' => [
            'delete' => [
                'confirm' => 'Supprimer',
                'disclaimer' => 'Ce contenu ne sera pas supprimé mais déplacé dans la corbeille.',
                'move-to-trash' => 'Mettre à la corbeille',
                'title' => 'Supprimer',
            ],
            'destroy' => [
                'confirm' => 'Supprimer définitivement',
                'destroy-permanently' => 'Supprimer définitivement',
                'disclaimer' => 'Ce contenu ne pourra pas être restauré.',
                'title' => 'Supprimer définitivement',
            ],
        ],
        'dropdown' => [
            'delete' => 'Supprimer',
            'destroy' => 'Supprimer définitivement',
            'duplicate' => 'Dupliquer',
            'edit' => 'Modifier',
            'publish' => 'Publier',
            'feature' => 'Mettre en avant',
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
            'no' => 'Non',
            'yes' => 'Oui',
            'not-set' => 'Sans valeur',
        ],
        'languages' => 'Langues',
        'listing-empty-message' => 'Il n’y a aucun contenu pour le moment.',
        'paginate' => [
            'rows-per-page' => 'Lignes par page:',
        ],
        'bulk-selected-item' => 'contenu sélectionné',
        'bulk-selected-items' => 'contenus sélectionnés',
        'reorder' => [
            'success' => ':modelTitle: position mise à jour !',
            'error' => ':modelTitle: position non mise à jour. Une erreur est survenue !',
        ],
        'restore' => [
            'success' => ':modelTitle restauré !',
            'error' => ':modelTitle non restauré. Une erreur est survenue !',
        ],
        'bulk-restore' => [
            'success' => ':modelTitle: plusieurs contenus restaurés !',
            'error' => ':modelTitle: plusieurs contenus non restaurés. Une erreur est survenue !',
        ],
        'force-delete' => [
            'success' => ':modelTitle détruit !',
            'error' => ':modelTitle non détruit. Une erreur est survenue !',
        ],
        'bulk-force-delete' => [
            'success' => ':modelTitle: plusieurs contenus détruits !',
            'error' => ':modelTitle: plusieurs contenus non détruits. Une erreur est survenue !',
        ],
        'delete' => [
            'success' => ':modelTitle déplacé dans la corbeille !',
            'error' => ':modelTitle non déplacé dans la corbeille. Une erreur est survenue !',
        ],
        'bulk-delete' => [
            'success' => ':modelTitle: plusieurs contenus déplacés dans la corbeille !',
            'error' => ':modelTitle: plusieurs contenus non déplacés dans la corbeille. Une erreur est survenue !',
        ],
        'duplicate' => [
            'success' => ':modelTitle dupliqué avec succès !',
            'error' => ':modelTitle n’a pas été dupliqué. Une erreur est survenue !',
        ],
        'publish' => [
            'unpublished' => ':modelTitle dépublié !',
            'published' => ':modelTitle publié !',
            'error' => ':modelTitle n’a pas été publié. Une erreur est survenue !',
        ],
        'featured' => [
            'unfeatured' => ':modelTitle n’est plus mis en avant !',
            'featured' => ':modelTitle est mis en avant !',
            'error' => ':modelTitle n’est pas mis en avant. Une erreur est survenue !',
        ],
        'bulk-featured' => [
            'unfeatured' => ':modelTitle: la sélection n’est plus mise en avant !',
            'featured' => ':modelTitle: la sélection est mise en avant !',
            'error' => ':modelTitle: la sélection n’a pas été mise en avant. Une erreur est survenue !',
        ],
        'bulk-publish' => [
            'unpublished' => ':modelTitle la sélection est dépubliée !',
            'published' => ':modelTitle la sélection est publiée !',
            'error' => ':modelTitle la sélection n’a pas été publiée. Une erreur est survenue !',
        ],
        'filters' => [
            'all-label' => 'Tous les :label',
        ],
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
        'insert' => 'Insérer',
        'sidebar' => [
            'alt-text' => 'Texte alternatif',
            'caption' => 'Légende',
            'clear' => 'Annuler',
            'dimensions' => 'Dimensions',
            'empty-text' => 'Aucun fichier sélectionné',
            'files-selected' => 'fichiers sélectionnés',
            'tags' => 'Tags',
        ],
        'title' => 'Galerie de médias',
        'update' => 'Mettre à jour',
        'unused-filter-label' => 'Non utilisé seulement',
        'no-tags-found' => 'Désolé, aucun tag trouvé.',
        'dialogs' => [
            'delete' => [
                'delete-media-title' => 'Supprimer le média',
                'delete-media-desc' => 'Êtes-vous sûr ?<br />Ce changement ne peut être annulé.',
                'delete-media-confirm' => 'Supprimer',
                'title' => 'Êtes-vous sûr ?',
                'allow-delete-multiple-medias' => 'Certains fichiers sont utilisés par du contenu et ne peuvent pas être supprimés. Voulez-vous supprimer les autres ?',
                'allow-delete-one-media' => 'Ce fichier est utilisé par du contenu et ne peut pas être supprimé. Voulez-vous supprimer les autres ?',
                'dont-allow-delete-multiple-medias' => 'Ces fichiers sont utilisés par du contenu et ne peuvent pas être supprimés.',
                'dont-allow-delete-one-media' => 'Ce fichier est utilisé par du contenu et ne peut pas être supprimé.',
            ],
            'replace' => [
                'replace-media-title' => 'Remplacer le média',
                'replace-media-desc' => 'Êtes-vous sûr ?<br />Ce changement ne peut être annulé.',
                'replace-media-confirm' => 'Remplacer',
            ],
        ],
        'types' => [
            'single' => [
                'image' => 'image',
                'video' => 'vidéo',
                'file' => 'fichier',
            ],
            'multiple' => [
                'image' => 'images',
                'video' => 'vidéos',
                'file' => 'fichiers',
            ],
        ],
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
            'button' => 'Mettre à jour',
            'title' => 'Mettre à jour',
        ],
        'done' => [
            'button' => 'Continuer',
        ],
    ],
    'nav' => [
        'admin' => 'Admin',
        'cms-users' => 'Utilisateurs du CMS',
        'logout' => 'Déconnexion',
        'media-library' => 'Galerie de médias',
        'settings' => 'Paramètres',
        'close-menu' => 'Fermer le menu',
        'profile' => 'Profile',
        'open-live-site' => 'Ouvrir le site',
    ],
    'notifications' => [
        'reset' => [
            'action' => 'Réinitialisation du mot de passe',
            'content' => 'Vous recevez cet e-mail car nous avons reçu une réinitialisation de mot de passe. Si vous n’avez pas demandé de réinitialisation de mot de passe, aucune action n’est requise.',
            'subject' => ':appName | Mot de passe oublié',
        ],
        'welcome' => [
            'action' => 'Choisissez votre propre mot de passe',
            'content' => 'Vous recevez cet e-mail car un compte a été créé pour vous sur :nom.',
            'title' => 'Bienvenue',
            'subject' => ':appName | Bienvenue',
        ],
    ],
    'overlay' => [
        'close' => 'Fermer',
    ],
    'previewer' => [
        'compare-view' => 'Comparer',
        'current-revision' => 'Active',
        'editor' => 'Éditeur',
        'last-edit' => 'Édité',
        'past-revision' => 'Ancienne',
        'restore' => 'Restaurer',
        'revision-history' => 'Historique des révisions',
        'single-view' => 'Prévisualiser les changements',
        'title' => 'Prévisualiser les changements',
        'unsaved' => 'Prévisualisation de vos changements non sauvegardés',
        'drag-and-drop' => 'Glisser et déposer des blocs',
    ],
    'publisher' => [
        'cancel' => 'Annuler',
        'current' => 'Active',
        'end-date' => 'Date de fin',
        'immediate' => 'Maintenant',
        'languages' => 'Langues',
        'languages-published' => 'Publiés',
        'last-edit' => 'Édité',
        'preview' => 'Prévisualiser les changements',
        'publish' => 'Publier',
        'publish-close' => 'Publier et fermer',
        'publish-new' => 'Publier et créer un nouveau',
        'published-on' => 'Publié le',
        'restore-draft' => 'Restaurer en tant que brouillon',
        'restore-draft-close' => 'Restaurer en tant que brouillon et fermer',
        'restore-draft-new' => 'Restaurer en tant que brouillon et créer un nouveau',
        'restore-live' => 'Restaurer avec le statut publié',
        'restore-live-close' => 'Restaurer avec le statut publié et fermer',
        'restore-live-new' => 'Restaurer avec le statut publié et créer un nouveau',
        'restore-message' => 'Vous modifiez actuellement une ancienne révision de ce contenu (enregistrée par :user le :date). Apportez les modifications nécessaires et cliquez sur restaurer pour enregistrer une nouvelle révision.',
        'restore-success' => 'Révision restaurée.',
        'revisions' => 'Révisions',
        'save' => 'Enregistrer comme brouillon',
        'save-close' => 'Enregistrer comme brouillon et fermer',
        'save-new' => 'Enregistrer comme brouillon et créer un nouveau',
        'save-success' => 'Contenu sauvegardé. Tout va bien !',
        'start-date' => 'Date de début',
        'switcher-title' => 'Statut',
        'update' => 'Mettre à jour',
        'update-close' => 'Mettre à jour et fermer',
        'update-new' => 'Mettre à jour et créer un nouveau',
        'parent-page' => 'Page parente',
        'review-status' => 'Statut de révision',
        'visibility' => 'Visibilité',
        'scheduled' => 'Planifié',
        'expired' => 'Expiré',
        'unsaved-changes' => 'Il y a des changements non sauvegardés',
        'draft-revision' => 'Sauvegarder en tant que brouillon de révision',
        'draft-revision-close' => 'Sauvegarder en tant que brouillon de révision et fermer',
        'draft-revision-new' => 'Sauvegarder en tant que brouillon de révision et créer un nouveau',
        'draft-revisions-available' => 'Vous visualisez actuellement la version publiée de ce contenu. Il y a des brouillons de révisions plus récents disponibles.',
        'editing-draft-revision' => 'Vous modifiez actuellement un brouillon de révision de ce contenu. Apportez les modifications nécessaires et cliquez sur Sauvegarder en tant que révision ou Publier.',
    ],
    'select' => [
        'empty-text' => 'Désolé, aucune correspondance trouvée.',
    ],
    'uploader' => [
        'dropzone-text' => 'ou faites glisser de nouveaux fichiers ici',
        'upload-btn-label' => 'Ajouter',
    ],
    'user-management' => [
        '2fa' => 'Identification à 2 facteurs',
        '2fa-description' => 'Veuillez scanner ce code QR avec une application compatible avec Google Authenticator et entrez votre mot de passe unique ci-dessous avant de le soumettre. Consultez la liste des applications compatibles <a href=":link" target="_blank" rel="noopener">ici</a>.',
        '2fa-disable' => 'Entrez votre mot de passe unique pour désactiver l’authentification à 2 facteurs',
        'active' => 'Actif',
        'cancel' => 'Annuler',
        'content-fieldset-label' => 'Paramètres utilisateur',
        'description' => 'Description',
        'disabled' => 'Désactivé',
        'edit-modal-title' => 'Éditer le nom d’utilisateur',
        'email' => 'Email',
        'enable-user' => 'Activer l’utilisateur',
        'enable-user-and-close' => 'Activer l’utilisateur et fermer',
        'enable-user-and-create-new' => 'Activer l’utilisateur et créer un nouveau',
        'enabled' => 'Activé',
        'language' => 'Langue',
        'language-placeholder' => 'Sélectionner une langue',
        'name' => 'Nom',
        'otp' => 'Mot de passe à usage unique',
        'profile-image' => 'Image de profil',
        'role' => 'Rôle',
        'role-placeholder' => 'Sélectionner un rôle',
        'title' => 'Titre',
        'trash' => 'Corbeille',
        'update' => 'Mettre à jour',
        'update-and-close' => 'Mettre à jour et fermer',
        'update-and-create-new' => 'Mettre à jour et créer un nouveau',
        'update-disabled-and-close' => 'Mettre à jour l’utilisateur désactivé et fermer',
        'update-disabled-user' => 'Mettre à jour l’utilisateur désactivé',
        'update-disabled-user-and-create-new' => 'Mettre à jour l’utilisateur désactivé et créer un nouveau',
        'user-image' => 'Image',
        'users' => 'Utilisateurs',
        'force-2fa-disable' => 'Désactiver la double authentification',
        'force-2fa-disable-description' => 'Entrer le texte affiché dans le champ pour désactiver la double authentification pour cet utilisateur',
        'force-2fa-disable-challenge' => 'Désactiver la double authentification pour :user',
        'pending' => 'En attente',
        'activation-pending' => 'Activation en attente',
    ],
    'settings' => [
        'update' => 'Mettre à jour',
        'cancel' => 'Annuler',
        'fieldset-label' => 'Éditer paramètres',
    ],
    'permissions' => [
        'roles' => [
            'title' => 'Rôles',
            'published' => 'Activé',
            'draft' => 'Désactivé',
        ],
        'groups' => [
            'title' => 'Groupes',
            'published' => 'Activé',
            'draft' => 'Désactivé',
        ],
    ],
];
