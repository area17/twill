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
        'password' => 'Пароль',
        'forgot-password' => 'Восстановить пароль',
        'login' => 'Войти',
        'login-title' => 'Войти',
    ],
    'dashboard' => [
        'search-placeholder' => 'Поиск всего...',
        'empty-message' => 'У вас еще нет активности.',
        'all-activity' => 'Все активность',
        'my-activity' => 'Моя активность',
        'create-new' => 'Создать',
        'my-drafts' => 'Мои черновики',
        'statitics' => 'Статистика',
    ],
    'footer' => [
        'version' => 'Версия',
    ],
    'form' => [
        'content' => 'Содержимое',
        'editor' => 'Редактор',
    ],
    'listing' => [
        'filter' => [
            'all-items' => 'Все',
            'mine' => 'Мои',
            'published' => 'Опубликованные',
            'draft' => 'Черновики',
            'trash' => 'Удаленные',
        ],
        'dropdown' => [
            'edit' => 'Изменить',
            'unpublish' => 'Отменить публикацию',
            'publish' => 'Опубликовать',
            'unfeature' => 'Убрать с выделенных',
            'feature' => 'Выделить',
            'restore' => 'Восстановить',
            'destroy' => 'Уничтожить',
            'delete' => 'Удалить',
            'duplicate' => 'Дублировать',
        ],
        'add-new-button' => 'Добавить',
        'listing-empty-message' => 'Ничего нет.',
        'languages' => 'Языки',
    ],
    'main' => [
        'published' => 'Опубликован',
        'draft' => 'Черновик',
        'create' => 'Создать',
        'update' => 'Обновить',
        'title' => 'Заголовок',
    ],
    'modal' => [
        'title-field' => 'Заголовок',
        'permalink-field' => 'Постоянная ссылка',
        'create' => [
            'title' => 'Добавить новый',
            'button' => 'Создать',
            'create-another' => 'Создать и добавить другую',
        ],
        'update' => [
            'title' => 'Обновить',
        ],
    ],
    'nav' => [
        'media-library' => 'Медиа библиотека',
        'cms-users' => 'Пользователи CMS',
        'settings' => 'Настройки',
        'logout' => 'Выйти',
        'admin' => 'Администратор',
    ],
    'media-library' => [
        'title' => 'Медиа библиотека',
        'images' => 'Изображения',
        'files' => 'Файлы',
        'filter-select-label' => 'Фильтровать по тегу',
        'sidebar' => [
            'empty-text' => 'Файлы не выделены',
            'files-selected' => 'файлов выделено',
            'clear' => 'Снять выделение',
            'dimensions' => 'Уровни',
        ],
    ],
    'filter' => [
        'search-placeholder' => 'Поиск',
        'toggle-label' => 'Фильтр',
        'apply-btn' => 'Применить',
        'clear-btn' => 'Очистить',
    ],
    'select' => [
        'empty-text' => 'Нет соответствующих параметров.',
    ],
    'uploader' => [
        'dropzone-text' => 'или перетащите сюда файлы.',
        'upload-btn-label' => 'Загрузите',
    ],
    'fields' => [
        'medias' => [
            'btn-label' => 'Прикрепить изображение',
        ],
        'block-editor' => [
            'collapse-all' => 'Раскрыть все',
            'expend-all' => 'Скрыть',
            'open-in-editor' => 'Открыть в редакторе',
            'create-another' => 'Создать другой',
            'delete' => 'Удалить',
            'add-content' => 'Добавить содержимое',
            'preview' => 'Предварительный просмотр',
            'loading' => 'Загрузка',
        ],
        'browser' => [
            'attach' => 'Прикрепить',
            'add-label' => 'Добавить',
        ],
        'files' => [
            'add-label' => 'Добавить',
        ],
    ],
    'user-management' => [
        'users' => 'Пользователи',
        'active' => 'Активный',
        'disabled' => 'Отключенный',
        'enabled' => 'Включенный',
        'trash' => 'Корзина',
        'user-image' => 'Изображение',
        'name' => 'Имя',
        'email' => 'Email',
        'role' => 'Роль',
        'content-fieldset-label' => 'Настройки пользователя',
        'edit-modal-title' => 'Изменить имя',
        'update-disabled-user' => 'Обновить отключенного пользователя',
        'update-disabled-and-close' => 'Обновить отключенного пользователя и закрыть',
        'update-disabled-user-and-create-new' => 'Обновить отключенного пользователя и создать еще',
        'enable-user' => 'Включить пользователя',
        'enable-user-and-close' => 'Включить пользователя и закрыть',
        'enable-user-and-create-new' => 'Включить пользователя и создать еще',
        'update' => 'Обновить',
        'update-and-close' => 'Обновить и закрыть',
        'update-and-create-new' => 'Обновить и создать еще',
        'cancel' => 'Отмена',
    ],
    'publisher' => [
        'switcher-title' => 'Статус',
        'save' => 'Сохранить как черновик',
        'save-close' => 'Сохранить как черновик и закрыть',
        'save-new' => 'Сохранить как черновик и создать еще',
        'publish' => 'Опубликовать',
        'publish-close' => 'Опубликовать и закрыть',
        'publish-new' => 'Опубликовать и создать еще',
        'update' => 'Обновить',
        'update-close' => 'Обновить и закрыть',
        'update-new' => 'Обновить и создать еще',
        'cancel' => 'Отмена',
    ],
];
