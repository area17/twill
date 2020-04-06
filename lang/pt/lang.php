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
        'password' => 'Palavra-passe',
        'forgot-password' => 'Esqueci a palavra-passe',
        'login' => 'Fazer login',
        'login-title' => 'Entrar',
    ],
    'dashboard' => [
        'search-placeholder' => 'Procurar em tudo...',
        'empty-message' => 'Você ainda não tem atividades.',
        'all-activity' => 'Todas as atividades',
        'my-activity' => 'Suas atividades',
        'create-new' => 'Criar nova',
        'my-drafts' => 'Meus rascunhos',
        'statitics' => 'Estatísticas',
    ],
    'footer' => [
        'version' => 'Versão',
    ],
    'form' => [
        'content' => 'Conteúdo',
        'editor' => 'Editor',
    ],
    'listing' => [
        'filter' => [
            'all-items' => 'Tdos os itens',
            'mine' => 'Meus',
            'published' => 'Publicado',
            'draft' => 'Rascunho',
            'trash' => 'Lixeira',
        ],
        'dropdown' => [
            'edit' => 'Editar',
            'unpublish' => 'Despublicar',
            'publish' => 'Publicar',
            'unfeature' => 'Remover realce',
            'feature' => 'Realçar',
            'restore' => 'Restaurar',
            'destroy' => 'Destruir',
            'delete' => 'Deletar',
            'duplicate' => 'Duplicar',
        ],
        'add-new-button' => 'Criar novo',
        'listing-empty-message' => 'Esta lista ainda está vazia.',
        'languages' => 'Idiomas',
    ],
    'main' => [
        'published' => 'No ar',
        'draft' => 'Rascunho',
        'create' => 'Criar',
        'update' => 'Atualizar',
        'title' => 'Título',
    ],
    'modal' => [
        'title-field' => 'Título',
        'permalink-field' => 'Link permanente',
        'create' => [
            'title' => 'Criar novo',
            'button' => 'Criar',
            'create-another' => 'Gravar e criar outro',
        ],
        'update' => [
            'title' => 'Atualizar',
        ],
    ],
    'nav' => [
        'media-library' => 'Biblioteca multimédia',
        'cms-users' => 'Utilizadores do CMS',
        'settings' => 'Configurações',
        'logout' => 'Sair',
        'admin' => 'Administrador',
    ],
    'media-library' => [
        'title' => 'Biblioteca multimédia',
        'images' => 'Imagens',
        'files' => 'Ficheiros',
        'filter-select-label' => 'Fitrar por tag',
        'sidebar' => [
            'empty-text' => 'Nenhum ficheiro selecionado',
            'files-selected' => 'ficheiros selecionados',
            'clear' => 'Limpar',
            'dimensions' => 'Dimensões',
        ],
    ],
    'filter' => [
        'search-placeholder' => 'Buscar',
        'toggle-label' => 'Filtrar',
        'apply-btn' => 'Aplicar',
        'clear-btn' => 'Limpar',
    ],
    'select' => [
        'empty-text' => 'Desculpe, nada foi encontrado.',
    ],
    'uploader' => [
        'dropzone-text' => 'ou solte novos ficheiros aqui',
        'upload-btn-label' => 'Criar novo',
    ],
    'fields' => [
        'medias' => [
            'btn-label' => 'Anexar imagem',
        ],
        'block-editor' => [
            'collapse-all' => 'Recolher tudo',
            'expend-all' => 'Expandir tudo',
            'open-in-editor' => 'Abrir no editor',
            'create-another' => 'Criar outro',
            'delete' => 'Deletar',
            'add-content' => 'Adicionar conteúdo',
            'preview' => 'Pré-visualizar',
            'loading' => 'Carregando',
        ],
        'browser' => [
            'attach' => 'Anexar',
            'add-label' => 'Adicionar',
        ],
        'files' => [
            'add-label' => 'Adicionar',
        ],
    ],
    'user-management' => [
        'users' => 'Utilizadores',
        'active' => 'Ativo',
        'disabled' => 'Desabilitado',
        'enabled' => 'Habilitado',
        'trash' => 'Lixeira',
        'user-image' => 'Imagem',
        'name' => 'Nome',
        'email' => 'Correio eletrónico',
        'role' => 'Função',
        'content-fieldset-label' => 'Configurações do utilizador',
        'edit-modal-title' => 'Alterar nome do utilizador',
        'update-disabled-user' => 'Atualizar utilizador desabilitado',
        'update-disabled-and-close' => 'Atualizar desabilitado e fechar',
        'update-disabled-user-and-create-new' => 'Atualizar utilizador desabilitado e criar outro',
        'enable-user' => 'Habilitar utilizador',
        'enable-user-and-close' => 'Habilitar utilizador e fechar',
        'enable-user-and-create-new' => 'Habilitar utilizador e criar outro',
        'update' => 'Atualizar',
        'update-and-close' => 'Atualizar e fechar',
        'update-and-create-new' => 'Atualizar e criar outro',
        'cancel' => 'Cancelar',
    ],
    'publisher' => [
        'switcher-title' => 'Situação',
        'save' => 'Salvar como rascunho',
        'save-close' => 'Salvar como rascunho e fechar',
        'save-new' => 'Salvar como rascunho e criar outro',
        'publish' => 'Publicar',
        'publish-close' => 'Publicar e fechar',
        'publish-new' => 'Publicar e criar outro',
        'update' => 'Atualizar',
        'update-close' => 'Atualizar e fechar',
        'update-new' => 'Atualizar e criar outro',
        'cancel' => 'Cancelar',
    ],
];
