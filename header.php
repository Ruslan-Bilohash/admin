<?php
// admin/header.php
// Полный файл с компактными кнопками перевода и полной мультиязычностью
// Дата: 25 декабря 2025

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Проверка авторизации администратора
if (!isAdmin()) {
    header("Location: /admin/login.php");
    exit;
}

// Загружаем переводы
$tr = load_admin_translations();

// Подсчёт новых заказов (безопасно)
$stmt = $conn->prepare("SELECT COUNT(*) FROM shop_orders WHERE status = ?");
$status = 'ожидает';
$stmt->bind_param("s", $status);
$stmt->execute();
$stmt->bind_result($new_orders_count);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(substr(getLanguage(), 0, 2)); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo $tr['admin_panel'] ?? 'Администратор Pro Website'; ?></title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Свои стили -->
    <link rel="stylesheet" href="/admin/css/style.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Sortable.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.6/Sortable.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>
<body>
    <div class="d-flex">

        <!-- Боковая панель -->
        <nav class="sidebar bg-dark text-white p-3" id="sidebar">
            <div class="text-center mb-3">
                <h4 class="text-white fw-bold mb-3">
                    <center>Website 🚀<br><?php echo $tr['website_management'] ?? 'Управление'; ?></center>
                </h4>

                <a href="/" class="text-warning text-decoration-none mb-2 d-block" target="_blank">
                    <i class="fas fa-globe me-1"></i> <?php echo $tr['to_the_site'] ?? 'На сайт'; ?>
                </a>

                <!-- Очень компактные кнопки перевода (только флаги) -->
                <div class="mb-2 d-flex justify-content-center gap-1 flex-nowrap overflow-auto">
                    <a href="?lang=ru" 
                       class="btn btn-xs <?php echo getLanguage() === 'ru' ? 'btn-primary' : 'btn-outline-light'; ?>" 
                       title="Русский" 
                       style="padding: 0.2rem 0.4rem; font-size: 0.7rem; min-width: 36px;">
                        🇷🇺
                    </a>
                    <a href="?lang=ua" 
                       class="btn btn-xs <?php echo getLanguage() === 'ua' ? 'btn-primary' : 'btn-outline-light'; ?>" 
                       title="Українська" 
                       style="padding: 0.2rem 0.4rem; font-size: 0.7rem; min-width: 36px;">
                        🇺🇦
                    </a>
                    <a href="?lang=en" 
                       class="btn btn-xs <?php echo getLanguage() === 'en' ? 'btn-primary' : 'btn-outline-light'; ?>" 
                       title="English" 
                       style="padding: 0.2rem 0.4rem; font-size: 0.7rem; min-width: 36px;">
                        EN
                    </a>
                    <a href="?lang=no" 
                       class="btn btn-xs <?php echo getLanguage() === 'no' ? 'btn-primary' : 'btn-outline-light'; ?>" 
                       title="Norsk" 
                       style="padding: 0.2rem 0.4rem; font-size: 0.7rem; min-width: 36px;">
                        🇳🇴
                    </a>
                    <a href="?lang=lt" 
                       class="btn btn-xs <?php echo getLanguage() === 'lt' ? 'btn-primary' : 'btn-outline-light'; ?>" 
                       title="Lietuvių" 
                       style="padding: 0.2rem 0.4rem; font-size: 0.7rem; min-width: 36px;">
                        🇱🇹
                    </a>
                </div>

                <!-- Кнопка выхода -->
                <a href="/admin/logout.php" class="btn btn-danger w-100">
                    <?php echo $tr['logout'] ?? 'Выйти'; ?>
                </a>
            </div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="/admin/index.php?module=dashboard" class="nav-link <?php echo ($module ?? '') === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt me-2"></i> <?php echo $tr['main'] ?? 'Главная'; ?>
                    </a>
                </li>

                <!-- Магазин -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module ?? '', [
                        'shop_dashboard', 'shop_add_product', 'shop_product', 'shop_category',
                        'shop_order', 'shop_delivery', 'shop_pay', 'shop_settings', 'shop_setting_footer'
                    ]) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-store me-2"></i> <?php echo $tr['shop'] ?? 'Магазин'; ?>
                        <?php if ($new_orders_count > 0): ?>
                            <span class="badge bg-danger ms-2"><?php echo $new_orders_count; ?></span>
                        <?php else: ?>
                            <span class="badge bg-success ms-2">0</span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/admin/index.php?module=shop_dashboard"><i class="fas fa-tachometer-alt me-2"></i><?php echo $tr['shop_dashboard'] ?? 'Информационная панель'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=shop_add_product"><i class="fas fa-box-open me-2"></i><?php echo $tr['add_product'] ?? 'Добавить товар'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=shop_product"><i class="fas fa-boxes me-2"></i><?php echo $tr['all_products'] ?? 'Все товары'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=shop_category"><i class="fas fa-folder me-2"></i><?php echo $tr['categories'] ?? 'Категории'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=shop_order">
                            <i class="fas fa-shopping-bag me-2"></i><?php echo $tr['orders'] ?? 'Заказы'; ?>
                            <?php if ($new_orders_count > 0): ?>
                                <span class="badge bg-danger ms-2"><?php echo $new_orders_count; ?></span>
                            <?php endif; ?>
                        </a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=shop_delivery"><i class="fas fa-truck me-2"></i><?php echo $tr['delivery'] ?? 'Доставка'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=shop_settings"><i class="fas fa-cogs me-2"></i><?php echo $tr['shop_settings'] ?? 'Настройки магазина'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=shop_setting_footer"><i class="fas fa-arrow-down me-2"></i><?php echo $tr['footer_settings'] ?? 'Подвал магазина'; ?></a></li>
                    </ul>
                </li>

                <!-- Бронирования -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module ?? '', ['booking_manager', 'booking', 'booking_settings']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-calendar-alt me-2"></i> <?php echo $tr['bookings'] ?? 'Бронирования'; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/admin/index.php?module=booking_manager"><i class="fas fa-building me-2"></i><?php echo $tr['booking_management'] ?? 'Управление объектами'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=booking"><i class="fas fa-calendar-alt me-2"></i><?php echo $tr['booking_list'] ?? 'Бронирования'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=booking_settings"><i class="fas fa-cog me-2"></i><?php echo $tr['booking_settings'] ?? 'Настройки бронирований'; ?></a></li>
                    </ul>
                </li>

                <!-- Тендеры -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module ?? '', ['tenders', 'tenders_add', 'tenders_edit', 'categories', 'cities', 'prices']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-gavel me-2"></i> <?php echo $tr['tenders'] ?? 'Тендеры'; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/admin/index.php?module=tenders_add"><i class="fas fa-plus me-2"></i><?php echo $tr['add_tender'] ?? 'Добавить тендер'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=tenders"><i class="fas fa-list-ul me-2"></i><?php echo $tr['tenders_list'] ?? 'Тендеры'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=categories"><i class="fas fa-folder me-2"></i><?php echo $tr['categories'] ?? 'Категории'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=cities"><i class="fas fa-city me-2"></i><?php echo $tr['cities'] ?? 'Города'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=prices"><i class="fas fa-dollar-sign me-2"></i><?php echo $tr['prices'] ?? 'Прайсы'; ?></a></li>
                    </ul>
                </li>

                <!-- Новости -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module ?? '', ['news_list', 'news_add', 'news_edit', 'news_settings', 'news_settings_lang', 'news_categories']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-newspaper me-2"></i> <?php echo $tr['news'] ?? 'Новости'; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/admin/index.php?module=news_list"><i class="fas fa-list-ul me-2"></i><?php echo $tr['news_list'] ?? 'Список новостей'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=news_add"><i class="fas fa-plus me-2"></i><?php echo $tr['news_add'] ?? 'Добавить новость'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=news_settings"><i class="fas fa-cog me-2"></i><?php echo $tr['news_settings'] ?? 'Настройки новостей'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=news_settings_lang"><i class="fas fa-language me-2"></i><?php echo $tr['news_multilanguage'] ?? 'Мультиязычность'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=news_categories"><i class="fas fa-folder-open me-2"></i><?php echo $tr['news_categories'] ?? 'Категории'; ?></a></li>
                    </ul>
                </li>

                <!-- Настройки -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module ?? '', [
                        'settings', 'settings_color', 'settings_form', 'carusel', 'seo', 'users', 'admins',
                        'shop_pay', 'files', 'backup', 'send_email'
                    ]) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cogs me-2"></i> <?php echo $tr['settings'] ?? 'Настройки'; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/admin/index.php?module=settings"><i class="fas fa-sliders-h me-2"></i><?php echo $tr['general_settings'] ?? 'Общие настройки'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=shop_pay"><i class="fas fa-credit-card me-2"></i><?php echo $tr['payment_settings'] ?? 'Платежные настройки'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=files"><i class="fas fa-file-alt me-2"></i><?php echo $tr['file_editor'] ?? 'Редактор файлов'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=backup"><i class="fas fa-database me-2"></i><?php echo $tr['mysql_backup'] ?? 'Backup MySQL'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=users"><i class="fas fa-users me-2"></i><?php echo $tr['users'] ?? 'Пользователи'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=admins"><i class="fas fa-user-shield me-2"></i><?php echo $tr['admins'] ?? 'Администраторы'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=send_email"><i class="fas fa-envelope me-2"></i><?php echo $tr['email_broadcast'] ?? 'Рассылка Email'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=settings_color"><i class="fas fa-palette me-2"></i><?php echo $tr['color_settings'] ?? 'Цвет'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=settings_form"><i class="fas fa-ruler me-2"></i><?php echo $tr['form_settings'] ?? 'Формы'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=carusel"><i class="fas fa-images me-2"></i><?php echo $tr['carousel'] ?? 'Управление каруселью'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=carusel-brand"><i class="fas fa-images me-2"></i><?php echo $tr['brands_carousel'] ?? 'Карусель брендов'; ?></a></li>
                    </ul>
                </li>

                <!-- SEO -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module ?? '', ['sitemap', 'seo', 'shop_seo', 'perehody']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-chart-line me-2"></i> <?php echo $tr['seo_optimization'] ?? 'SEO Оптимизация'; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/admin/index.php?module=sitemap"><i class="fas fa-sitemap me-2"></i><?php echo $tr['sitemap'] ?? 'Карта сайта'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=seo"><i class="fas fa-search-plus me-2"></i><?php echo $tr['seo_main_page'] ?? 'SEO Главная страница'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=shop_seo"><i class="fas fa-store-alt me-2"></i><?php echo $tr['seo_shop'] ?? 'SEO Магазина'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=perehody"><i class="fas fa-link me-2"></i><?php echo $tr['transitions'] ?? 'Переходы'; ?></a></li>
                    </ul>
                </li>

                <!-- Обратная связь -->
                <li class="nav-item">
                    <a href="/admin/index.php?module=feedback" class="nav-link <?php echo ($module ?? '') === 'feedback' ? 'active' : ''; ?>">
                        <i class="fas fa-envelope-open-text me-2"></i> <?php echo $tr['feedback'] ?? 'Обратная связь'; ?>
                        <?php
                        $unread_count = $conn->query("SELECT COUNT(*) FROM feedback WHERE type = 'message' AND is_read = 0")->fetch_row()[0] ?? 0;
                        if ($unread_count > 0): ?>
                            <span class="badge bg-danger ms-2"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <!-- Сервисные страницы -->
                <li class="nav-item">
                    <a href="/admin/index.php?module=page" class="nav-link <?php echo ($module ?? '') === 'page' ? 'active' : ''; ?>">
                        <i class="fas fa-file-code me-2"></i> <?php echo $tr['service_pages'] ?? 'Сервисные страницы'; ?>
                    </a>
                </li>

                <!-- Безопасность -->
                <li class="nav-item">
                    <a href="/admin/index.php?module=security_check" class="nav-link <?php echo ($module ?? '') === 'security_check' ? 'active' : ''; ?>">
                        <i class="fas fa-shield-virus me-2"></i> <?php echo $tr['security'] ?? 'Безопасность'; ?>
                    </a>
                </li>

                <!-- API -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module ?? '', ['api', 'nova_poshta_settings']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-plug me-2"></i> <?php echo $tr['api'] ?? 'API'; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/admin/index.php?module=api"><i class="fas fa-code me-2"></i><?php echo $tr['third_party_api'] ?? 'Сторонние API'; ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?module=nova_poshta_settings"><i class="fas fa-truck-fast me-2"></i><?php echo $tr['nova_poshta'] ?? 'Новая Почта'; ?></a></li>
                    </ul>
                </li>

                <!-- Кэш -->
                <li class="nav-item">
                    <a href="/admin/index.php?module=cache" class="nav-link <?php echo ($module ?? '') === 'cache' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt me-2"></i> <?php echo $tr['cache'] ?? 'Кеш (cache)'; ?>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Кнопка сворачивания боковой панели -->
        <button class="btn btn-dark toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-expanded="false" aria-controls="sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Основной контент -->
        <div class="content flex-grow-1 p-4">
            <!-- Здесь начинается контент модулей -->
			