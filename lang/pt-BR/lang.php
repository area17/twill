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
        'forgot-password' => 'Esqueci a senha',
        'login' => 'Fazer login',
        'login-title' => 'Entrar',
        'password' => 'Senha',
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
        'files' => 'Arquivos',
        'filter-select-label' => 'Fitrar por tag',
        'images' => 'Imagens',
        'sidebar' => [
            'clear' => 'Limpar',
            'dimensions' => 'Dimensões',
            'empty-text' => 'Nenhum arquivo selecionado',
            'files-selected' => 'arquivos selecionados',
        ],
        'title' => 'Biblioteca de mídias',
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
        'cms-users' => 'Usuários do CMS',
        'logout' => 'Sair',
        'media-library' => 'Biblioteca de mídias',
        'settings' => 'Configurações',
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
        'dropzone-text' => 'ou solte novos arquivos aqui',
        'upload-btn-label' => 'Criar novo',
    ],
    'user-management' => [
        'active' => 'Ativo',
        'cancel' => 'Cancelar',
        'content-fieldset-label' => 'Configurações do usuário',
        'disabled' => 'Desabilitado',
        'edit-modal-title' => 'Alterar nome do usuário',
        'email' => 'E-mail',
        'enable-user' => 'Habilitar usuário',
        'enable-user-and-close' => 'Habilitar usuário e fechar',
        'enable-user-and-create-new' => 'Habilitar usuário e criar outro',
        'enabled' => 'Habilitado',
        'name' => 'Nome',
        'role' => 'Função',
        'trash' => 'Lixeira',
        'update' => 'Atualizar',
        'update-and-close' => 'Atualizar e fechar',
        'update-and-create-new' => 'Atualizar e criar outro',
        'update-disabled-and-close' => 'Atualizar desabilitado e fechar',
        'update-disabled-user' => 'Atualizar usuário desabilitado',
        'update-disabled-user-and-create-new' => 'Atualizar usuário desabilitado e criar outro',
        'user-image' => 'Imagem',
        'users' => 'Usuários',
    ],
];
