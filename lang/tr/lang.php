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
        'back-to-login' => 'Girişe Geri Dön',
        'choose-password' => 'Parola seçin',
        'email' => 'Eposta',
        'forgot-password' => 'Parolamı unuttum',
        'login' => 'Giriş',
        'login-title' => 'Giriş',
        'oauth-link-title' => ':provider hesabınıza bağlamak için şifrenizi tekrar girin',
        'otp' => 'Tek kullanımlık parola',
        'password' => 'Parola',
        'password-confirmation' => 'Parolayı onayla',
        'reset-password' => 'Parolayı sıfırla',
        'reset-send' => 'Parola sıfırlama bağlantısını gönder',
        'verify-login' => 'Girişi doğrula',
    ],
    'buckets' => [
        'intro' => 'Bugün neyi öne çıkarmak istersin?',
        'none-available' => 'Öğe bulunamadı.',
        'none-featured' => 'Öne çıkan öğe yok.',
        'publish' => 'Yayınla',
        'source-title' => 'Mevcut öğeler',
    ],
    'dashboard' => [
        'all-activity' => 'Tüm aktiviteler',
        'create-new' => 'Yeni oluştur',
        'empty-message' => 'Henüz bir aktiviteniz yok.',
        'my-activity' => 'Aktivitelerim',
        'my-drafts' => 'Taslaklarım',
        'search-placeholder' => 'Her şeyi ara...',
        'statitics' => 'İstatistikler',
        'search' => [
            'loading' => 'Yükleniyor...',
            'no-result' => 'Sonuç bulunamadı.',
            'last-edit' => 'Son düzenleme',
        ],
        'activities' => [
            'created' => 'Oluşturulma',
            'updated' => 'Güncellenme',
            'unpublished' => 'Yayından kaldırılma',
            'published' => 'Yayınlanma',
            'featured' => 'Öne çıkar',
            'unfeatured' => 'Öne çıkarma',
            'restored' => 'Geri yüklendi',
            'deleted' => 'Silindi',
        ],
        'activity-row' => [
            'edit' => 'Düzenle',
            'view-permalink' => 'Kalıcı bağlantıyı görüntüle',
            'by' => 'göre',
        ],
        'unknown-author' => 'Bilinmiyor',
    ],
    'dialog' => [
        'cancel' => 'İptal',
        'ok' => 'Tamam',
        'title' => 'Çöpe Taşı',
    ],
    'editor' => [
        'cancel' => 'İptal',
        'delete' => 'Sil',
        'done' => 'Tamamlandı',
        'title' => 'İçerik editörü',
    ],
    'emails' => [
        'all-rights-reserved' => 'Her hakkı saklıdır.',
        'hello' => 'Merhaba!',
        'problems' => '":actionText" düğmesini tıklamada sorun yaşıyorsanız, aşağıdaki URL\'yi kopyalayıp web tarayıcınıza yapıştırın: [:url](:url)',
        'regards' => 'Saygılarımızla,',
    ],
    'fields' => [
        'block-editor' => [
            'add-content' => 'İçerik ekle',
            'collapse-all' => 'Hepsini daralt',
            'create-another' => 'Başka bir tane oluştur',
            'delete' => 'Sil',
            'expand-all' => 'Hepsini genişlet',
            'loading' => 'Yükleniyor',
            'open-in-editor' => 'Editörde aç',
            'preview' => 'Önizleme',
            'add-item' => 'Öğe ekle',
            'clone-block' => 'Bloğu kopyala',
        ],
        'browser' => [
            'add-label' => 'Ekle',
            'attach' => 'Ataç',
        ],
        'files' => [
            'add-label' => 'Ekle',
        ],
        'generic' => [
            'switch-language' => 'Dil değiştir',
        ],
        'map' => [
            'hide' => 'Gizle&nbsp;map',
            'show' => 'Göster&nbsp;map',
        ],
        'medias' => [
            'btn-label' => 'Resim ekle',
            'crop' => 'Kes',
            'crop-edit' => 'Görüntü kırpmayı düzenle',
            'crop-list' => 'kırp',
            'crop-save' => 'Güncelle',
            'delete' => 'Sil',
            'download' => 'İndir',
            'edit-close' => 'Bilgileri kapat',
            'edit-info' => 'Bilgileri düzenle',
            'original-dimensions' => 'Orjinal',
            'alt-text' => 'Alt Metni',
            'caption' => 'Altyazı',
            'video-url' => 'Video URL (isteğe bağlı)',
        ],
    ],
    'filter' => [
        'apply-btn' => 'Uygula',
        'clear-btn' => 'Temizle',
        'search-placeholder' => 'Ara',
        'toggle-label' => 'Filtrele',
    ],
    'footer' => [
        'version' => 'Versiyon',
    ],
    'form' => [
        'content' => 'İçerik',
        'dialogs' => [
            'delete' => [
                'confirm' => 'Sil',
                'confirmation' => 'Emin misiniz?<br />Bu değişiklik geri alınamaz.',
                'delete-content' => 'İçeriği sil',
                'title' => 'İçeriği sil',
            ],
        ],
        'editor' => 'Editör',
    ],
    'lang-manager' => [
        'published' => 'Canlı',
    ],
    'lang-switcher' => [
        'edit-in' => 'Düzenle',
    ],
    'listing' => [
        'add-new-button' => 'Yeni ekle',
        'bulk-actions' => 'Toplu eylemler',
        'bulk-clear' => 'Temizle',
        'columns' => [
            'featured' => 'Öne çıkan',
            'name' => 'Ad',
            'published' => 'Yayınlanan',
            'show' => 'Göster',
            'thumbnail' => 'Küçük resim',
        ],
        'dialogs' => [
            'delete' => [
                'confirm' => 'Sil',
                'disclaimer' => 'Bu öğe silinmeyecek, fakat çöğ sepetine taşınacak.',
                'move-to-trash' => 'Çöpe sepetine taşı',
                'title' => 'Öğeyi sil',
            ],
            'destroy' => [
                'confirm' => 'Yok et',
                'destroy-permanently' => 'Kalıcı olarak yok et',
                'disclaimer' => 'Öğe artık geri yüklenemeyecek.',
                'title' => 'Öğeyi yok et',
            ],
        ],
        'dropdown' => [
            'delete' => 'Sil',
            'destroy' => 'Yok et',
            'duplicate' => 'Çoğalt',
            'edit' => 'Düzenle',
            'publish' => 'Yayınla',
            'feature' => 'Öne çıkan',
            'restore' => 'Geri yükle',
            'unfeature' => 'Öne çıkartma',
            'unpublish' => 'Yayınlama',
        ],
        'filter' => [
            'all-items' => 'Tüm öğeler',
            'draft' => 'Taslak',
            'mine' => 'Benim',
            'published' => 'Yayınlandı',
            'trash' => 'Çöp',
        ],
        'languages' => 'Diller',
        'listing-empty-message' => 'Burada henüz bir öğe yok.',
        'paginate' => [
            'rows-per-page' => 'Sayfa başına satır sayısı:',
        ],
        'bulk-selected-item' => 'öğe seçildi',
        'bulk-selected-items' => 'öğe seçildi',
        'reorder' => [
            'success' => ':modelTitle sıralama değiştirildi!',
            'error' => ':modelTitle sıralama değiştirilemedi. Bazı şeyler yolunda gitmedi!',
        ],
        'restore' => [
            'success' => ':modelTitle geri yüklendi!',
            'error' => ':modelTitle geri yüklenemedi. Bazı şeyler yolunda gitmedi!',
        ],
        'bulk-restore' => [
            'success' => ':modelTitle öğeler geri yüklendi!',
            'error' => ':modelTitle öğeler geri yüklenemedi. Bazı şeyler yolunda gitmedi!',
        ],
        'force-delete' => [
            'success' => ':modelTitle yok edildi!',
            'error' => ':modelTitle yok edilemedi. Bazı şeyler yolunda gitmedi!',
        ],
        'bulk-force-delete' => [
            'success' => ':modelTitle öğeler yok edildi!',
            'error' => ':modelTitle öğeler yok edilemedi. Bazı şeyler yolunda gitmedi!',
        ],
        'delete' => [
            'success' => ':modelTitle çöpe taşındı!',
            'error' => ':modelTitle çöpe taşınamadı. Bazı şeyler yolunda gitmedi!',
        ],
        'bulk-delete' => [
            'success' => ':modelTitle öğeler çöpe taşındı!',
            'error' => ':modelTitle öğeler çöpe taşınamadı. Bazı şeyler yolunda gitmedi!',
        ],
        'duplicate' => [
            'success' => ':modelTitle başarıyla çoğaltıldı!',
            'error' => ':modelTitle çoğaltılamadı. Bazı şeyler yolunda gitmedi!',
        ],
        'publish' => [
            'unpublished' => ':modelTitle yayından kaldırıldı!',
            'published' => ':modelTitle yayınlandı!',
            'error' => ':modelTitle yayınlanamadı. Bazı şeyler yolunda gitmedi!',
        ],
        'featured' => [
            'unfeatured' => ':modelTitle öne çıkartılmadı!',
            'featured' => ':modelTitle öne çıkartıldı!',
            'error' => ':modelTitle öne çıkartılamadı. Bazı şeyler yolunda gitmedi!',
        ],
        'bulk-featured' => [
            'unfeatured' => ':modelTitle öğeler öne çıkartılmadı!',
            'featured' => ':modelTitle öğeler öne çıkartıldı!',
            'error' => ':modelTitle öğeler öne çıkartılamadı. Bazı şeyler yolunda gitmedi!',
        ],
        'bulk-publish' => [
            'unpublished' => ':modelTitle öğeler yayından kaldırıldı!',
            'published' => ':modelTitle öğeler yayınlandı!',
            'error' => ':modelTitle öğeler yayınlanamadı. Bazı şeyler yolunda gitmedi!',
        ],
    ],
    'main' => [
        'create' => 'Oluştur',
        'draft' => 'Taslak',
        'published' => 'Canlı',
        'title' => 'Başlık',
        'update' => 'Güncelle',
    ],
    'media-library' => [
        'files' => 'Dosyalar',
        'filter-select-label' => 'Etikete göre filtrele',
        'images' => 'Resimler',
        'insert' => 'Ekle',
        'sidebar' => [
            'alt-text' => 'Alt metni',
            'caption' => 'Altyazı',
            'clear' => 'Temizle',
            'dimensions' => 'Boyutlar',
            'empty-text' => 'Dosya seçilmedi',
            'files-selected' => 'seçili dosyalar',
            'tags' => 'Etiketler',
        ],
        'title' => 'Medya Kütüphanesi',
        'update' => 'Güncelle',
        'unused-filter-label' => 'Sadece kullanılmayanları görüntüle',
        'no-tags-found' => 'Üzgünüz, etiket bulunamadı.',
        'dialogs' => [
            'delete' => [
                'delete-media-title' => 'Medyayı sil',
                'delete-media-desc' => 'Emin misiniz ?<br />Bu değişiklik geri alınamaz.',
                'delete-media-confirm' => 'Sil',
                'title' => 'Emin misiniz ?',
                'allow-delete-multiple-medias' => 'Bazı dosyalar kullanıldığı için silinemiyor. Diğerlerini silmek istiyor musunuz ?',
                'allow-delete-one-media' => 'Bu dosya kullanıldığı için silinemiyor. Diğerlerini silmek istiyor musunuz ?',
                'dont-allow-delete-multiple-medias' => 'Bu dosyalar kullanıldığı için silinemiyor.',
                'dont-allow-delete-one-media' => 'Bu dosya kullanıldığı için silinemiyor.',
            ],
            'replace' => [
                'replace-media-title' => 'Medyayı değiştir',
                'replace-media-desc' => 'Emin misiniz ?<br />Bu değişiklik geri alınamaz.',
                'replace-media-confirm' => 'Değiştir',
            ],
        ],
        'types' => [
            'single' => [
                'image' => 'resim',
                'video' => 'video',
                'file' => 'dosya',
            ],
            'multiple' => [
                'image' => 'resimler',
                'video' => 'videolar',
                'file' => 'dosyalar',
            ],
        ],
    ],
    'modal' => [
        'create' => [
            'button' => 'Oluştur',
            'create-another' => 'Oluştur ve diğerini ekle',
            'title' => 'Yeni ekle',
        ],
        'permalink-field' => 'Kalıcı bağlantı',
        'title-field' => 'Başlık',
        'update' => [
            'button' => 'Güncelle',
            'title' => 'Güncelle',
        ],
        'done' => [
            'button' => 'Tamam',
        ],
    ],
    'nav' => [
        'admin' => 'Yönetici',
        'cms-users' => 'CMS Kullanıcıları',
        'logout' => 'Çıkış',
        'media-library' => 'Medya Kütüphanesi',
        'settings' => 'Ayarlar',
        'close-menu' => 'Menüyü kapat',
    ],
    'notifications' => [
        'reset' => [
            'action' => 'Parolayı sıfırla',
            'content' => 'Bu e-postayı, parola sıfırlama işlemi aldığımız için alıyorsunuz. Parola sıfırlama talebinde bulunmadıysanız, başka bir işlem yapmanız gerekmez.',
            'subject' => ':appName | Parola Sıfırlama',
        ],
        'welcome' => [
            'action' => 'Kendi parolanızı belirleyin',
            'content' => 'Bu e-postayı, sizin için :name üzerinde bir hesap oluşturulduğu için alıyorsunuz.',
            'title' => 'Hoş geldiniz',
            'subject' => ':appName | Hoş Geldiniz',
        ],
    ],
    'overlay' => [
        'close' => 'Kapat',
    ],
    'previewer' => [
        'compare-view' => 'Görünümü karşılaştır',
        'current-revision' => 'Geçerli',
        'editor' => 'Editör',
        'last-edit' => 'Son düzenleme',
        'past-revision' => 'Geçmiş',
        'restore' => 'Geri yükle',
        'revision-history' => 'Revizyon Geçmişi',
        'single-view' => 'Tek görünüm',
        'title' => 'Değişiklikleri önizle',
        'unsaved' => 'Kaydedilmemiş değişikliklerinizi önizliyorsunuz',
        'drag-and-drop' => 'Soldaki panelden içeriği sürükleyip bırakın',
    ],
    'publisher' => [
        'cancel' => 'İptal',
        'current' => 'Geçerli',
        'end-date' => 'Bitiş tarihi',
        'immediate' => 'Acil',
        'languages' => 'Diller',
        'languages-published' => 'Canlı',
        'last-edit' => 'Son düzenleme',
        'preview' => 'Değişiklikleri önizle',
        'publish' => 'Yayınla',
        'publish-close' => 'Yayınla ve kapat',
        'publish-new' => 'Yayınla ve yeni oluştur',
        'published-on' => 'Yayınlama tarihi',
        'restore-draft' => 'Taslak olarak geri yükle',
        'restore-draft-close' => 'Taslak olarak geri yükle ve kapat',
        'restore-draft-new' => 'Taslak olarak geri yükle ve yeni oluştur',
        'restore-live' => 'Yayınlanmış olarak geri yükle',
        'restore-live-close' => 'Yayınlanmış olarak geri yükle ve kapat',
        'restore-live-new' => 'Yayınlanmış olarak geri yükle ve yeni oluştur',
        'restore-message' => 'Şu anda bu içeriğin daha eski bir revizyonunu düzenliyorsunuz (:user tarafından :date tarihinde kaydedildi). Gerekirse değişiklik yapın ve yeni bir düzeltmeyi kaydetmek için geri yükle\'yi tıklayın.',
        'restore-success' => 'Revizyon geri yüklendi.',
        'revisions' => 'Revizyonlar',
        'save' => 'Taslak olarak kaydet',
        'save-close' => 'Taslak olarak kaydet ve kapat',
        'save-new' => 'Taslak olarak kaydet ve yeni oluştur',
        'save-success' => 'İçerik kaydedildi. Herşey normal!',
        'start-date' => 'Başlangıç tarihi',
        'switcher-title' => 'Durum',
        'update' => 'Güncelle',
        'update-close' => 'Güncelle ve kapat',
        'update-new' => 'Güncelle ve yeni oluştur',
        'parent-page' => 'Ana sayfa',
        'review-status' => 'Durumu gözden geçir',
        'visibility' => 'Görünürlük',
    ],
    'select' => [
        'empty-text' => 'Üzgünüz, eşleşen seçenek yok.',
    ],
    'uploader' => [
        'dropzone-text' => 'yada yeni dosyaları buraya bırakın',
        'upload-btn-label' => 'Yeni ekle',
    ],
    'user-management' => [
        '2fa' => '2 faktörlü kimlik doğrulama',
        '2fa-description' => 'Lütfen bu QR kodunu Google Authenticator uyumlu bir uygulama ile tarayın ve göndermeden önce aşağıya tek kullanımlık şifrenizi girin. Uyumlu uygulamaların listesine <a href=":link" target="_blank" rel="noopener">buradan</a> bakın.',
        '2fa-disable' => '2 faktörlü kimlik doğrulamayı devre dışı bırakmak için tek kullanımlık parolanızı girin',
        'active' => 'Aktif',
        'cancel' => 'İptal',
        'content-fieldset-label' => 'Kullanıcı ayarları',
        'description' => 'Açıklama',
        'disabled' => 'Engelli',
        'edit-modal-title' => 'Kullanıcı adını düzenle',
        'email' => 'Eposta',
        'enable-user' => 'Kullanıcıyı etkinleştir',
        'enable-user-and-close' => 'Kullanıcıyı etkinleştir ve kapat',
        'enable-user-and-create-new' => 'Kullanıcıyı etkinleştir ve yeni oluştur',
        'enabled' => 'Etkin',
        'language' => 'Dil',
        'language-placeholder' => 'Dil seç',
        'name' => 'Ad',
        'otp' => 'Tek kullanımlık parola',
        'profile-image' => 'Profil resmi',
        'role' => 'Rol',
        'role-placeholder' => 'Rol seç',
        'title' => 'Başlık',
        'trash' => 'Çöp',
        'update' => 'Güncelle',
        'update-and-close' => 'Güncelle ve kapat',
        'update-and-create-new' => 'Güncelle ve yeni oluştur',
        'update-disabled-and-close' => 'Engelli olarak güncelle ve kapat',
        'update-disabled-user' => 'Engelli kullanıcıyı güncelle',
        'update-disabled-user-and-create-new' => 'Engelli kullanıcıyı güncelle ve yeni oluştur',
        'user-image' => 'Resim',
        'users' => 'Kullanıcılar',
    ],
    'settings' => [
        'update' => 'Güncelle',
        'cancel' => 'İptal',
        'fieldset-label' => 'Ayarları düzenle',
    ],
];
