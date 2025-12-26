<?php
// Файл: /admin/google_translate.php

// Запускаем сессию, если она еще не начата
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Список поддерживаемых языков и их кодов
$languages = [
    'en' => ['name' => 'English', 'flag' => '🇬🇧', 'country' => ['US', 'GB']],
    'ru' => ['name' => 'Русский', 'flag' => '🇷🇺', 'country' => ['RU', 'UA', 'BY']],
    'de' => ['name' => 'Deutsch', 'flag' => '🇩🇪', 'country' => ['DE', 'AT']],
    'fr' => ['name' => 'Français', 'flag' => '🇫🇷', 'country' => ['FR']],
    'es' => ['name' => 'Español', 'flag' => '🇪🇸', 'country' => ['ES', 'MX']],
];

// Локализация интерфейса
$translations = [
    'en' => [
        'dashboard' => 'Dashboard',
        'shop' => 'Shop',
        'bookings' => 'Bookings',
        'tenders' => 'Tenders',
        'news' => 'News',
        'settings' => 'Settings',
        'seo' => 'SEO Optimization',
        'feedback' => 'Feedback',
        'pages' => 'Service Pages',
        'security' => 'Security',
        'api' => 'API',
        'cache' => 'Cache',
        'logout' => 'Logout',
        'to_site' => 'To Website',
    ],
    'ru' => [
        'dashboard' => 'Главная',
        'shop' => 'Магазин',
        'bookings' => 'Бронирования',
        'tenders' => 'Тендеры',
        'news' => 'Новости',
        'settings' => 'Настройки',
        'seo' => 'SEO Оптимизация',
        'feedback' => 'Обратная связь',
        'pages' => 'Сервисные страницы',
        'security' => 'Безопасность',
        'api' => 'API',
        'cache' => 'Кеш',
        'logout' => 'Выйти',
        'to_site' => 'На сайт',
    ],
    'de' => [
        'dashboard' => 'Armaturenbrett',
        'shop' => 'Geschäft',
        'bookings' => 'Buchungen',
        'tenders' => 'Ausschreibungen',
        'news' => 'Nachrichten',
        'settings' => 'Einstellungen',
        'seo' => 'SEO-Optimierung',
        'feedback' => 'Feedback',
        'pages' => 'Dienstseiten',
        'security' => 'Sicherheit',
        'api' => 'API',
        'cache' => 'Cache',
        'logout' => 'Ausloggen',
        'to_site' => 'Zur Website',
    ],
    'fr' => [
        'dashboard' => 'Tableau de bord',
        'shop' => 'Boutique',
        'bookings' => 'Réservations',
        'tenders' => 'Appels d\'offres',
        'news' => 'Nouvelles',
        'settings' => 'Paramètres',
        'seo' => 'Optimisation SEO',
        'feedback' => 'Retour',
        'pages' => 'Pages de service',
        'security' => 'Sécurité',
        'api' => 'API',
        'cache' => 'Cache',
        'logout' => 'Déconnexion',
        'to_site' => 'Vers le site',
    ],
    'es' => [
        'dashboard' => 'Tablero',
        'shop' => 'Tienda',
        'bookings' => 'Reservas',
        'tenders' => 'Licitaciones',
        'news' => 'Noticias',
        'settings' => 'Configuraciones',
        'seo' => 'Optimización SEO',
        'feedback' => 'Comentarios',
        'pages' => 'Páginas de servicio',
        'security' => 'Seguridad',
        'api' => 'API',
        'cache' => 'Caché',
        'logout' => 'Cerrar sesión',
        'to_site' => 'Al sitio web',
    ],
];

// Функция для получения информации о стране по IP
function getCountryByIP() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $url = "https://ipapi.co/{$ip}/json/";
    $response = @file_get_contents($url);
    
    if ($response === false) {
        return 'US'; // Запасной вариант
    }
    
    $data = json_decode($response, true);
    return isset($data['country_code']) ? $data['country_code'] : 'US';
}

// Определяем язык пользователя
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : null;

if (!$current_lang) {
    $country = getCountryByIP();
    foreach ($languages as $code => $lang) {
        if (in_array($country, $lang['country'])) {
            $current_lang = $code;
            break;
        }
    }
    $current_lang = $current_lang ?: 'en';
    $_SESSION['lang'] = $current_lang;
}

// Обработка выбора языка из формы
if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $languages)) {
    $current_lang = $_GET['lang'];
    $_SESSION['lang'] = $current_lang;
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Функция для получения переведенной строки
function t($key) {
    global $translations, $current_lang;
    return isset($translations[$current_lang][$key]) ? $translations[$current_lang][$key] : $key;
}

?>

<!-- HTML для выпадающего меню выбора языка -->
<div class="language-selector dropdown ms-3">
    <button class="btn btn-outline-light dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <span style="margin-right: 5px;"><?php echo $languages[$current_lang]['flag']; ?></span>
        <?php echo $languages[$current_lang]['name']; ?>
    </button>
    <ul class="dropdown-menu" aria-labelledby="languageDropdown">
        <?php foreach ($languages as $code => $lang): ?>
            <li>
                <a class="dropdown-item <?php echo $code === $current_lang ? 'active fw-bold' : ''; ?>" href="?lang=<?php echo $code; ?>">
                    <span style="margin-right: 5px;"><?php echo $lang['flag']; ?></span>
                    <?php echo $lang['name']; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<!-- Стили для выделения текущего языка -->
<style>
.language-selector .dropdown-menu {
    min-width: 150px;
}
.language-selector .dropdown-item.active {
    background-color: #007bff;
    color: white;
}
.language-selector .dropdown-toggle {
    border-radius: 5px;
}
.language-selector span {
    vertical-align: middle;
}
</style>

<!-- Подключение Google Translate -->
<script type="text/javascript">
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'ru',
        includedLanguages: '<?php echo implode(',', array_keys($languages)); ?>',
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
        autoDisplay: false
    }, 'google_translate_element');
    
    // Автоматический перевод на текущий язык
    function applyTranslation() {
        var select = document.querySelector('.goog-te-combo');
        if (select) {
            select.value = '<?php echo $current_lang; ?>';
            select.dispatchEvent(new Event('change'));
            // Принудительное обновление перевода для динамических элементов
            document.querySelectorAll('[data-translate]').forEach(function(element) {
                element.textContent = element.getAttribute('data-translate-' + '<?php echo $current_lang; ?>');
            });
        } else {
            setTimeout(applyTranslation, 500); // Повторная попытка, если виджет еще не загружен
        }
    }
    setTimeout(applyTranslation, 1000);
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<!-- Пустой контейнер для Google Translate -->
<div id="google_translate_element" style="display: none;"></div>