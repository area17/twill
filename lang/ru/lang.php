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
        'forgot-password' => 'Восстановить пароль',
        'login' => 'Войти',
        'login-title' => 'Войти',
        'password' => 'Пароль',
    ],
    'dashboard' => [
        'all-activity' => 'Вся активность',
        'create-new' => 'Создать',
        'empty-message' => 'У вас еще нет активности.',
        'my-activity' => 'Моя активность',
        'my-drafts' => 'Мои черновики',
        'search-placeholder' => 'Поиск всего...',
        'statitics' => 'Статистика',
    ],
    'fields' => [
        'block-editor' => [
            'add-content' => 'Добавить содержимое',
            'collapse-all' => 'Раскрыть все',
            'create-another' => 'Создать другой',
            'delete' => 'Удалить',
            'expand-all' => 'Скрыть',
            'loading' => 'Загрузка',
            'open-in-editor' => 'Открыть в редакторе',
            'preview' => 'Предварительный просмотр',
        ],
        'browser' => [
            'add-label' => 'Добавить',
            'attach' => 'Прикрепить',
        ],
        'files' => [
            'add-label' => 'Добавить',
        ],
        'medias' => [
            'btn-label' => 'Прикрепить изображение',
        ],
    ],
    'filter' => [
        'apply-btn' => 'Применить',
        'clear-btn' => 'Очистить',
        'search-placeholder' => 'Поиск',
        'toggle-label' => 'Фильтр',
    ],
    'footer' => [
        'version' => 'Версия',
    ],
    'form' => [
        'content' => 'Содержимое',
        'editor' => 'Редактор',
    ],
    'listing' => [
        'add-new-button' => 'Добавить',
        'dropdown' => [
            'delete' => 'Удалить',
            'destroy' => 'Уничтожить',
            'duplicate' => 'Дублировать',
            'edit' => 'Изменить',
            'feature' => 'Выделить',
            'publish' => 'Опубликовать',
            'restore' => 'Восстановить',
            'unfeature' => 'Убрать с выделенных',
            'unpublish' => 'Отменить публикацию',
        ],
        'filter' => [
            'all-items' => 'Все',
            'draft' => 'Черновики',
            'mine' => 'Мои',
            'published' => 'Опубликованные',
            'trash' => 'Удаленные',
        ],
        'languages' => 'Языки',
        'listing-empty-message' => 'Ничего нет.',
    ],
    'main' => [
        'create' => 'Создать',
        'draft' => 'Черновик',
        'published' => 'Опубликован',
        'title' => 'Заголовок',
        'update' => 'Обновить',
    ],
    'media-library' => [
        'files' => 'Файлы',
        'filter-select-label' => 'Фильтровать по тегу',
        'images' => 'Изображения',
        'sidebar' => [
            'clear' => 'Снять выделение',
            'dimensions' => 'Уровни',
            'empty-text' => 'Файлы не выделены',
            'files-selected' => 'файлов выделено',
        ],
        'title' => 'Медиа библиотека',
    ],
    'modal' => [
        'create' => [
            'button' => 'Создать',
            'create-another' => 'Создать и добавить другую',
            'title' => 'Добавить новый',
        ],
        'permalink-field' => 'Постоянная ссылка',
        'title-field' => 'Заголовок',
        'update' => [
            'title' => 'Обновить',
        ],
    ],
    'nav' => [
        'admin' => 'Администратор',
        'cms-users' => 'Пользователи CMS',
        'logout' => 'Выйти',
        'media-library' => 'Медиа библиотека',
        'settings' => 'Настройки',
    ],
    'publisher' => [
        'cancel' => 'Отмена',
        'publish' => 'Опубликовать',
        'publish-close' => 'Опубликовать и закрыть',
        'publish-new' => 'Опубликовать и создать еще',
        'save' => 'Сохранить как черновик',
        'save-close' => 'Сохранить как черновик и закрыть',
        'save-new' => 'Сохранить как черновик и создать еще',
        'switcher-title' => 'Статус',
        'update' => 'Обновить',
        'update-close' => 'Обновить и закрыть',
        'update-new' => 'Обновить и создать еще',
    ],
    'select' => [
        'empty-text' => 'Нет соответствующих параметров.',
    ],
    'uploader' => [
        'dropzone-text' => 'или перетащите сюда файлы.',
        'upload-btn-label' => 'Загрузите',
    ],
    'user-management' => [
        'active' => 'Активные',
        'cancel' => 'Отмена',
        'content-fieldset-label' => 'Настройки пользователя',
        'disabled' => 'Отключенные',
        'edit-modal-title' => 'Изменить имя',
        'email' => 'Email',
        'enable-user' => 'Включить пользователя',
        'enable-user-and-close' => 'Включить пользователя и закрыть',
        'enable-user-and-create-new' => 'Включить пользователя и создать еще',
        'enabled' => 'Включенные',
        'name' => 'Имя',
        'role' => 'Роль',
        'trash' => 'В корзине',
        'update' => 'Обновить',
        'update-and-close' => 'Обновить и закрыть',
        'update-and-create-new' => 'Обновить и создать еще',
        'update-disabled-and-close' => 'Обновить отключенного пользователя и закрыть',
        'update-disabled-user' => 'Обновить отключенного пользователя',
        'update-disabled-user-and-create-new' => 'Обновить отключенного пользователя и создать еще',
        'user-image' => 'Изображение',
        'users' => 'Пользователи',
    ],
];
