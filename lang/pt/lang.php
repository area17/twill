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
        'email' => 'Correio eletrónico',
        'forgot-password' => 'Esqueci a palavra-passe',
        'login' => 'Fazer login',
        'login-title' => 'Entrar',
        'password' => 'Palavra-passe',
    ],
    'dashboard' => [
        'all-activity' => 'Todas as atividades',
        'create-new' => 'Criar nova',
        'empty-message' => 'Você ainda não tem atividades.',
        'my-activity' => 'Suas atividades',
        'my-drafts' => 'Meus rascunhos',
        'search-placeholder' => 'Procurar em tudo...',
        'statitics' => 'Estatísticas',
    ],
    'fields' => [
        'block-editor' => [
            'add-content' => 'Adicionar conteúdo',
            'collapse-all' => 'Recolher tudo',
            'create-another' => 'Criar outro',
            'delete' => 'Deletar',
            'expand-all' => 'Expandir tudo',
            'loading' => 'Carregando',
            'open-in-editor' => 'Abrir no editor',
            'preview' => 'Pré-visualizar',
        ],
        'browser' => [
            'add-label' => 'Adicionar',
            'attach' => 'Anexar',
        ],
        'files' => [
            'add-label' => 'Adicionar',
        ],
        'medias' => [
            'btn-label' => 'Anexar imagem',
        ],
    ],
    'filter' => [
        'apply-btn' => 'Aplicar',
        'clear-btn' => 'Limpar',
        'search-placeholder' => 'Buscar',
        'toggle-label' => 'Filtrar',
    ],
    'footer' => [
        'version' => 'Versão',
    ],
    'form' => [
        'content' => 'Conteúdo',
        'editor' => 'Editor',
    ],
    'listing' => [
        'add-new-button' => 'Criar novo',
        'dropdown' => [
            'delete' => 'Deletar',
            'destroy' => 'Destruir',
            'duplicate' => 'Duplicar',
            'edit' => 'Editar',
            'publish' => 'Publicar',
            'feature' => 'Realçar',
            'restore' => 'Restaurar',
            'unfeature' => 'Remover realce',
            'unpublish' => 'Despublicar',
        ],
        'filter' => [
            'all-items' => 'Tdos os itens',
            'draft' => 'Rascunho',
            'mine' => 'Meus',
            'published' => 'Publicado',
            'trash' => 'Lixeira',
        ],
        'languages' => 'Idiomas',
        'listing-empty-message' => 'Esta lista ainda está vazia.',
    ],
    'main' => [
        'create' => 'Criar',
        'draft' => 'Rascunho',
        'published' => 'No ar',
        'title' => 'Título',
        'update' => 'Atualizar',
    ],
    'media-library' => [
        'files' => 'Ficheiros',
        'filter-select-label' => 'Fitrar por tag',
        'images' => 'Imagens',
        'sidebar' => [
            'clear' => 'Limpar',
            'dimensions' => 'Dimensões',
            'empty-text' => 'Nenhum ficheiro selecionado',
            'files-selected' => 'ficheiros selecionados',
        ],
        'title' => 'Biblioteca multimédia',
    ],
    'modal' => [
        'create' => [
            'button' => 'Criar',
            'create-another' => 'Gravar e criar outro',
            'title' => 'Criar novo',
        ],
        'permalink-field' => 'Link permanente',
        'title-field' => 'Título',
        'update' => [
            'title' => 'Atualizar',
        ],
    ],
    'nav' => [
        'admin' => 'Administrador',
        'cms-users' => 'Utilizadores do CMS',
        'logout' => 'Sair',
        'media-library' => 'Biblioteca multimédia',
        'settings' => 'Configurações',
        'profile' => 'Profile',
    ],
    'publisher' => [
        'cancel' => 'Cancelar',
        'publish' => 'Publicar',
        'publish-close' => 'Publicar e fechar',
        'publish-new' => 'Publicar e criar outro',
        'save' => 'Salvar como rascunho',
        'save-close' => 'Salvar como rascunho e fechar',
        'save-new' => 'Salvar como rascunho e criar outro',
        'switcher-title' => 'Situação',
        'update' => 'Atualizar',
        'update-close' => 'Atualizar e fechar',
        'update-new' => 'Atualizar e criar outro',
    ],
    'select' => [
        'empty-text' => 'Desculpe, nada foi encontrado.',
    ],
    'uploader' => [
        'dropzone-text' => 'ou solte novos ficheiros aqui',
        'upload-btn-label' => 'Criar novo',
    ],
    'user-management' => [
        'active' => 'Ativo',
        'cancel' => 'Cancelar',
        'content-fieldset-label' => 'Configurações do utilizador',
        'disabled' => 'Desabilitado',
        'edit-modal-title' => 'Alterar nome do utilizador',
        'email' => 'Correio eletrónico',
        'enable-user' => 'Habilitar utilizador',
        'enable-user-and-close' => 'Habilitar utilizador e fechar',
        'enable-user-and-create-new' => 'Habilitar utilizador e criar outro',
        'enabled' => 'Habilitado',
        'name' => 'Nome',
        'role' => 'Função',
        'trash' => 'Lixeira',
        'update' => 'Atualizar',
        'update-and-close' => 'Atualizar e fechar',
        'update-and-create-new' => 'Atualizar e criar outro',
        'update-disabled-and-close' => 'Atualizar desabilitado e fechar',
        'update-disabled-user' => 'Atualizar utilizador desabilitado',
        'update-disabled-user-and-create-new' => 'Atualizar utilizador desabilitado e criar outro',
        'user-image' => 'Imagem',
        'users' => 'Utilizadores',
    ],
];
