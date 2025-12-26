<?php
// Настройка сессии для HTTPS (выполняется до session_start)
ini_set('session.cookie_secure', 1); // Только HTTPS
ini_set('session.cookie_httponly', 1); // Защита от XSS
ini_set('session.use_strict_mode', 1); // Строгий режим сессии
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Отладка: логируем сессию в начале
error_log("Сессия в admin/index.php: " . print_r($_SESSION, true));

// Проверяем, авторизован ли администратор
if (!isAdmin()) {
    error_log("isAdmin вернул false в index.php, редирект на /admin/login.php");
    header("Location: /admin/login.php");
    exit;
}

// Определяем текущий модуль
$module = $_GET['module'] ?? 'dashboard';
$module_file = $_SERVER['DOCUMENT_ROOT'] . "/admin/modules/{$module}.php";

// Включаем буферизацию вывода для модуля
ob_start();
if (file_exists($module_file)) {
    include $module_file;
    $content = ob_get_clean();
} else {
    ob_end_clean();
    $content = "<div class='container'><h1>Модуль не найден</h1></div>";
}

// Получаем текущие настройки
$settings = get_settings();
if ($settings === false || !is_array($settings)) {
    $settings = []; // Если настройки не загружены или не массив, используем пустой массив
}

// Устанавливаем значения по умолчанию для ключей, если их нет
$settings = array_merge([
    'logo_value' => null,
    'logo_text' => null,
    'logo_size' => 'medium',
    'logo_text_color' => '#000000',
    'navbar_brand_color' => '#ffffff',
    'header_color' => '#343a40',
    'site_nav_link_color' => '#000000',
    'footer_color' => '#343a40',
    'button_color' => '#007bff',
    'button_size' => 'medium',
    'button_shape' => 0,
    'show_logo' => 1
], $settings);

// Обработка настроек (модуль settings)
if ($module === 'settings' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $new_settings = [
        'logo_value' => $_POST['logo_value'] ?: null,
        'logo_text' => $_POST['logo_text'] ?: null,
        'logo_size' => $_POST['logo_size'] ?? 'medium',
        'logo_text_color' => $_POST['logo_text_color'] ?? '#000000',
        'navbar_brand_color' => $_POST['navbar_brand_color'] ?? '#ffffff',
        'header_color' => $_POST['header_color'] ?? '#343a40',
        'site_nav_link_color' => $_POST['site_nav_link_color'] ?? '#000000',
        'footer_color' => $_POST['footer_color'] ?? '#343a40',
        'button_color' => $_POST['button_color'] ?? '#007bff',
        'button_size' => $_POST['button_size'] ?? 'medium',
        'button_shape' => (int)($_POST['button_shape'] ?? 0),
        'show_logo' => isset($_POST['show_logo']) ? 1 : 0
    ];

    // Обработка загрузки изображения логотипа
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] === UPLOAD_ERR_OK) {
        $logo_value = upload_image($_FILES['logo_image'], $upload_dir);
        if (!$logo_value) {
            $error_message = "Ошибка при загрузке изображения. Разрешены только JPEG, PNG, GIF.";
        } else {
            $new_settings['logo_value'] = '/uploads/' . $logo_value;
        }
    } elseif (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] === UPLOAD_ERR_INI_SIZE) {
        $error_message = "Файл слишком большой.";
    }

    // Сохранение настроек в файл
    if (save_settings(array_merge($settings, $new_settings))) {
        header("Location: /admin/index.php?module=settings");
        exit;
    } else {
        $error_message = "Ошибка сохранения настроек в файл.";
    }
}

// Обработка удаления логотипа
if ($module === 'settings' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_logo'])) {
    if (isset($settings['logo_value']) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $settings['logo_value'])) {
        $file_path = $_SERVER['DOCUMENT_ROOT'] . $settings['logo_value'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $settings['logo_value'] = null;
        if (save_settings($settings)) {
            header("Location: /admin/index.php?module=settings");
            exit;
        } else {
            $error_message = "Ошибка при удалении логотипа.";
        }
    }
}

// Подключаем шапку, выводим контент и футер
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';
if (isset($content)) {
    if (isset($error_message)) {
        echo "<div class='alert alert-danger'>$error_message</div>";
    }
    echo $content;
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php';
?>