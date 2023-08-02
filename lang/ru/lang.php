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
        'back-to-login' => 'Вернуться к авторизации',
        'choose-password' => 'Введите пароль',
        'email' => 'Email',
        'forgot-password' => 'Восстановить пароль',
        'login' => 'Войти',
        'login-title' => 'Войти',
        'password' => 'Пароль',
        'password-confirmation' => 'Повторите пароль',
        'reset-send' => 'Отправить ссылку на восстановление пароля',
    ],
    'buckets' => [
        'intro' => 'Что вы хотите показать сегодня?',
        'none-available' => 'Нет доступных вариантов.',
        'publish' => 'Публикация',
        'source-title' => 'Доступные варианты',
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
    'dialog' => [
        'cancel' => 'Отмена',
        'ok' => 'ОК',
        'title' => 'Отправить в корзину',
    ],
    'editor' => [
        'cancel' => 'Отмена',
        'delete' => 'Удалить',
    ],
    'emails' => [
        'all-rights-reserved' => 'Все права защищены.',
        'hello' => 'Привет!',
        'problems' => 'Если у вас не нажимается кнопка ":actionText", скопируйте ссылку, и вставьте в браузер: [:url](:url)',
        'regards' => 'С уважением,',
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
        'generic' => [
            'switch-language' => 'Переключить язык',
        ],
        'map' => [
            'hide' => 'Скрыть&nbsp;карту',
            'show' => 'Показать&nbsp;карту',
        ],
        'medias' => [
            'btn-label' => 'Прикрепить изображение',
            'crop' => 'Кадрировать',
            'crop-edit' => 'Изменить кадрирование картинки',
            'crop-list' => 'кадрировать',
            'crop-save' => 'Обновить',
            'delete' => 'Удалить',
            'download' => 'Скачать',
            'edit-close' => 'Скрыть описание',
            'edit-info' => 'Изменить описание',
            'original-dimensions' => 'Оригинал',
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
        'dialogs' => [
            'delete' => [
                'confirm' => 'Удалить',
                'confirmation' => 'Вы уверены?</br> Это действие нельзя отменить.',
                'delete-content' => 'Удалить контент',
                'title' => 'Удалить контент',
            ],
        ],
        'editor' => 'Редактор',
    ],
    'listing' => [
        'add-new-button' => 'Добавить',
        'bulk-clear' => 'Очистить',
        'columns' => [
            'name' => 'Название',
            'published' => 'Опубликовано',
            'show' => 'Показать',
            'thumbnail' => 'Превью',
        ],
        'dialogs' => [
            'delete' => [
                'confirm' => 'Удалить',
                'disclaimer' => 'Элемент не был удалён, он перенесён в корзину.',
                'move-to-trash' => 'Перенести в корзину',
                'title' => 'Удалить элемент',
            ],
            'destroy' => [
                'confirm' => 'Уничтожить',
                'destroy-permanently' => 'Уничтожить навсегда',
                'disclaimer' => 'Элемент нельзя будет восстановить.',
                'title' => 'Уничтожить элемент',
            ],
        ],
        'dropdown' => [
            'delete' => 'Удалить',
            'destroy' => 'Уничтожить',
            'duplicate' => 'Дублировать',
            'edit' => 'Изменить',
            'publish' => 'Опубликовать',
            'feature' => 'Выделить',
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
        'paginate' => [
            'rows-per-page' => 'Строк на странице:',
        ],
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
            'alt-text' => 'Alt',
            'clear' => 'Снять выделение',
            'dimensions' => 'Уровни',
            'empty-text' => 'Файлы не выделены',
            'files-selected' => 'файлов выделено',
            'tags' => 'Теги',
        ],
        'title' => 'Медиа библиотека',
        'update' => 'Обновить',
        'unused-filter-label' => 'Показать только неиспользуемые',
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
            'button' => 'Обновить',
            'title' => 'Обновить',
        ],
    ],
    'nav' => [
        'admin' => 'Администратор',
        'cms-users' => 'Пользователи CMS',
        'logout' => 'Выйти',
        'media-library' => 'Медиа библиотека',
        'settings' => 'Настройки',
        'close-menu' => 'Закрыть меню',
        'profile' => 'Profile',
    ],
    'notifications' => [
        'reset' => [
            'action' => 'Сброс пароля',
            'content' => 'Вы получили это письмо, потому что мы получили запрос на сброс пароля. Если вы не запрашивали сброс пароля, никаких дальнейших действий не требуется.',
        ],
        'welcome' => [
            'content' => 'Вы получили это письмо, потому что для вас была создана учетная запись :name.',
            'title' => 'Добро пожаловать',
        ],
    ],
    'overlay' => [
        'close' => 'Закрыть',
    ],
    'previewer' => [
        'compare-view' => 'Сравнить',
        'current-revision' => 'Текущий',
        'editor' => 'Редактор',
        'last-edit' => 'Последняя редакция',
        'restore' => 'Восстановить',
        'revision-history' => 'История изменений',
        'title' => 'Предпросмотр изменений',
    ],
    'publisher' => [
        'cancel' => 'Отмена',
        'current' => 'Текущий',
        'end-date' => 'Окончание активности',
        'immediate' => 'Немедленно',
        'languages' => 'Языки',
        'last-edit' => 'Последнее изменение',
        'preview' => 'Предпросмотр изменений',
        'publish' => 'Опубликовать',
        'publish-close' => 'Опубликовать и закрыть',
        'publish-new' => 'Опубликовать и создать еще',
        'save' => 'Сохранить как черновик',
        'save-close' => 'Сохранить как черновик и закрыть',
        'save-new' => 'Сохранить как черновик и создать еще',
        'save-success' => 'Контент сохранён. Всё хорошо!',
        'start-date' => 'Начало активности',
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
        '2fa' => '2-фактороная аутентификация',
        'active' => 'Активные',
        'cancel' => 'Отмена',
        'content-fieldset-label' => 'Настройки пользователя',
        'description' => 'Описание',
        'disabled' => 'Отключенные',
        'edit-modal-title' => 'Изменить имя',
        'email' => 'Email',
        'enable-user' => 'Включить пользователя',
        'enable-user-and-close' => 'Включить пользователя и закрыть',
        'enable-user-and-create-new' => 'Включить пользователя и создать еще',
        'enabled' => 'Включенные',
        'language' => 'Язык',
        'language-placeholder' => 'Выберите язык',
        'name' => 'Имя',
        'otp' => 'Одноразовый пароль',
        'profile-image' => 'Изображение профиля',
        'role' => 'Роль',
        'role-placeholder' => 'Выберите роль',
        'title' => 'Заголовок',
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
