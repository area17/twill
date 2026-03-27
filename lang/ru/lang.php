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
        'activities' => [
            'created' => 'Создано',
            'updated' => 'Обновлено',
            'unpublished' => 'Снято с публикации',
            'published' => 'Опубликовано',
            'featured' => 'Featured',
            'unfeatured' => 'Unfeatured',
            'restored' => 'Восстановлено',
            'deleted' => 'Удалено',
            'login' => 'Login action',
            'logout' => 'Logout action',
            'duplicated' => 'Скопировано',
        ],
        'activity-row' => [
            'edit' => 'Редактировать',
            'view-permalink' => 'Показать ссылку',
            'by' => '. Редактор - ',
        ],
        'all-activity' => 'Вся активность',
        'create-new' => 'Создать',
        'empty-message' => 'У вас еще нет активности.',
        'my-activity' => 'Моя активность',
        'search' => [
            'loading' => 'Поиск…',
            'no-result' => 'Ничего не найдено',
            'last-edit' => 'Последнее редактирование',
        ],
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
        'cancel' => 'Закрыть',
        'delete' => 'Удалить',
        'done' => 'Готово',
        'title' => 'Редактор контента',
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
            'clone-block' => 'Клонировать',
            'collapse-all' => 'Свернуть всё',
            'create-another' => 'Создать другой',
            'delete' => 'Удалить',
            'expand-all' => 'Раскрыть всё',
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
            'crop-list' => 'кадрирование',
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
        'bulk-actions' => 'Выберите действие',
        'bulk-clear' => 'Очистить',
        'bulk-delete' => [
            'success' => 'Выбранные сущности успешно удалены',
        ],
        'bulk-force-delete' => [
            'success' => 'Выбранные записи удалены навсегда',
        ],
        'bulk-publish' => [
            'published' => 'Успешно опубликовано',
            'unpublished' => 'Успешно снято с публикации',
        ],
        'bulk-restore' => [
            'success' => 'Выбранные сущности успешно восстановлены',
        ],
        'bulk-selected-item' => 'выбрано',
        'bulk-selected-items' => 'выбрано',
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
        'filters' => [
            'all-label' => 'Все',
        ],
        'languages' => 'Языки',
        'listing-empty-message' => 'Ничего нет.',
        'paginate' => [
            'rows-per-page' => 'Строк на странице:',
        ],
        'delete' => [
            'success' => 'Успешно удалено',
        ],
        'force-delete' => [
            'success' => 'Запись удалена навсегда',
        ],
        'publish' => [
            'published' => 'Успешно опубликовано',
            'unpublished' => 'Успешно снято с публикации',
        ],
        'reorder' => [
            'success' => 'Порядок изменён',
            'error' => 'Не удалось изменить порядок',
        ],
        'restore' => [
            'success' => 'Успешно восстановлено',
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
        'dialogs' => [
            'delete' => [
                'delete-media-title' => 'Вы уверены что хотите удалить этот объект?',
                'delete-media-desc' => '<br>Объект будет удалён навсегда',
                'delete-media-confirm' => 'Удалить',
            ],
        ],
        'files' => 'Файлы',
        'filter-select-label' => 'Фильтровать по тегу',
        'images' => 'Изображения',
        'sidebar' => [
            'alt-text' => 'Alt',
            'clear' => 'Снять выделение',
            'dimensions' => 'Размеры',
            'empty-text' => 'Файлы не выделены',
            'files-selected' => 'файлов выделено',
            'tags' => 'Теги',
        ],
        'title' => 'Медиа библиотека',
        'types' => [
            'single' => [
                'video' => 'видео',
            ],
            'multiple' => [
                'video' => 'видео',
            ],
        ],
        'update' => 'Обновить',
        'unused-filter-label' => 'Показать только неиспользуемые',
        'video' => 'Видео',
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
        'profile' => 'Профиль',
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
        'drag-and-drop' => 'Перетаскивайте сюда блоки из панели слева',
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
        'revisions' => 'Предыдущие версии',
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
        'pending' => 'Ожидают активации',
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
    'wysiwyg' => [
        'link_window' => [
            'open_in_new_window' => 'Открыть в новом окне',
            'text' => 'Текст',
            'title' => 'Редактировать ссылку',
            'link' => 'Ссылка',
        ],
    ],
    'permissions' => [
        'roles' => [
            'title' => 'Роли',
            'published' => 'Роль опубликована',
            'draft' => 'Черновик',
        ],
    ],
];
