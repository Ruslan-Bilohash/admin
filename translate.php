<?php
// /admin/translate.php — Центральний перекладач адмін-панелі
// Використовується у всіх файлах адмінки через include

// Визначаємо поточну мову адмінки (зберігається в сесії)
$current_admin_lang = $_SESSION['admin_lang'] ?? 'ua';

// Доступні мови
$admin_langs = [
    'ua' => 'Українська',
    'en' => 'English',
    'no' => 'Norsk'
];

// Масив перекладів для адмін-панелі
$admin_trans = [
    'ua' => [
        'title'                => 'Адмін-панель — MapsMe Norway',
        'welcome'              => 'Ласкаво просимо, Адміністратор!',
        'manage_easy'          => 'Керуйте MapsMe Norway легко та швидко',
        'pending_ads'          => 'Оголошень на модерації',
        'active_ads'           => 'Опублікованих / активних оголошень',
        'total_users'          => 'Зареєстровано користувачів',
        'new_today'            => 'Нових сьогодні',
        'pending_news'         => 'Новин на модерації',
        'published_news'       => 'Опублікованих новин',
        'total_ads'            => 'Всього оголошень',
        'total_news'           => 'Всього новин',
        'db_status'            => 'Підключення до БД',
        'main'                 => 'Головна',
        'users'                => 'Користувачі',
        'ads'                  => 'Оголошення',
        'news'                 => 'Новини',
        'logout'               => 'Вийти',
        'view_pending'         => 'Переглянути на модерації',
        'manage_users'         => 'Керувати користувачами',
        'add_news'             => 'Додати новину',
    ],
    'en' => [
        'title'                => 'Admin Panel — MapsMe Norway',
        'welcome'              => 'Welcome, Administrator!',
        'manage_easy'          => 'Manage MapsMe Norway easily and quickly',
        'pending_ads'          => 'Ads pending moderation',
        'active_ads'           => 'Published / active ads',
        'total_users'          => 'Registered users',
        'new_today'            => 'New today',
        'pending_news'         => 'News pending moderation',
        'published_news'       => 'Published news',
        'total_ads'            => 'Total ads',
        'total_news'           => 'Total news',
        'db_status'            => 'Database connection',
        'main'                 => 'Dashboard',
        'users'                => 'Users',
        'ads'                  => 'Advertisements',
        'news'                 => 'News',
        'logout'               => 'Logout',
        'view_pending'         => 'View pending',
        'manage_users'         => 'Manage users',
        'add_news'             => 'Add news',
    ],
    'no' => [
        'title'                => 'Adminpanel — MapsMe Norway',
        'welcome'              => 'Velkommen, Administrator!',
        'manage_easy'          => 'Administrer MapsMe Norway enkelt og raskt',
        'pending_ads'          => 'Annonser til moderering',
        'active_ads'           => 'Publiserte / aktive annonser',
        'total_users'          => 'Registrerte brukere',
        'new_today'            => 'Nye i dag',
        'pending_news'         => 'Nyheter til moderering',
        'published_news'       => 'Publiserte nyheter',
        'total_ads'            => 'Totalt annonser',
        'total_news'           => 'Totalt nyheter',
        'db_status'            => 'Databaseforbindelse',
        'main'                 => 'Hovedside',
        'users'                => 'Brukere',
        'ads'                  => 'Annonser',
        'news'                 => 'Nyheter',
        'logout'               => 'Logg ut',
        'view_pending'         => 'Se ventende',
        'manage_users'         => 'Administrer brukere',
        'add_news'             => 'Legg til nyhet',
    ]
];

// Функція-помічник для перекладу
function t($key) {
    global $current_admin_lang, $admin_trans;
    return $admin_trans[$current_admin_lang][$key] ?? $key;
}

// Перемикання мови (додається в GET)
if (isset($_GET['admin_lang']) && array_key_exists($_GET['admin_lang'], $admin_langs)) {
    $_SESSION['admin_lang'] = $_GET['admin_lang'];
    $current_admin_lang = $_SESSION['admin_lang'];
    // Редирект без параметра
    $redirect = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: $redirect");
    exit;
}
?>
?>