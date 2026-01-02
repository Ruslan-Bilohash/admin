<?php
// /admin/header.php — Шапка адмін-панелі MapsMe Norway
// Оновлено: 2 січня 2026 року
// Автор: Руслан Білогаш
// ✅ Прапори мов у шапці з активним станом
// ✅ Повний набір перекладів (UA/EN/NO)
// ✅ Адаптивний дизайн, hover-ефекти, sticky header

// Масив перекладів для шапки адмінки
$admin_translations = [
    'ua' => [
        'title'               => 'Адмін-панель MapsMe Norway',
        'welcome'             => 'Ласкаво просимо, адміністраторе!',
        'pending_ads'         => 'Очікувані оголошення',
        'view_ads'            => 'Переглянути всі оголошення',
        'nav_home'            => 'На сайт',
        'nav_users'           => 'Користувачі',
        'nav_ads'             => 'Оголошення',
        'nav_news'            => 'Новини',
        'nav_logout'          => 'Вийти',
        'lang_switch'         => 'Мова інтерфейсу',
    ],
    'en' => [
        'title'               => 'MapsMe Norway Admin Panel',
        'welcome'             => 'Welcome, Administrator!',
        'pending_ads'         => 'Pending Ads',
        'view_ads'            => 'View All Ads',
        'nav_home'            => 'Back to Site',
        'nav_users'           => 'Users',
        'nav_ads'             => 'Ads',
        'nav_news'            => 'News',
        'nav_logout'          => 'Logout',
        'lang_switch'         => 'Interface Language',
    ],
    'no' => [
        'title'               => 'MapsMe Norway Admin Panel',
        'welcome'             => 'Velkommen, administrator!',
        'pending_ads'         => 'Ventende annonser',
        'view_ads'            => 'Se alle annonser',
        'nav_home'            => 'Tilbake til nettstedet',
        'nav_users'           => 'Brukere',
        'nav_ads'             => 'Annonser',
        'nav_news'            => 'Nyheter',
        'nav_logout'          => 'Logg ut',
        'lang_switch'         => 'Grensesnittspråk',
    ]
];

// Вибираємо переклади залежно від поточної мови
$t = $admin_translations[$current_lang] ?? $admin_translations['ua'];
?>

<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($t['title']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --dark: #0f172a;
            --gray: #6b7280;
            --light: #f1f5f9;
            --success: #10b981;
            --danger: #ef4444;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }
        header {
            background: linear-gradient(135deg, var(--dark), #172554);
            color: white;
            padding: clamp(1rem, 3vw, 1.4rem) 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            flex-wrap: wrap;
            gap: 1rem;
        }
        .logo {
            font-size: clamp(1.4rem, 4vw, 1.8rem);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        .logo i { font-size: 1.9rem; color: #60a5fa; }
        .nav-right {
            display: flex;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        nav a {
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        nav a:hover {
            color: #60a5fa;
            transform: translateY(-1px);
        }
        .lang-flags {
            display: flex;
            gap: 0.6rem;
        }
        .lang-flag {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 3px solid transparent;
            font-size: 1.8rem;
            line-height: 38px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.12);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
        }
        .lang-flag.active {
            border-color: #60a5fa;
            background: rgba(96,165,250,0.35);
            box-shadow: 0 0 0 4px rgba(96,165,250,0.25);
            transform: scale(1.12);
        }
        .lang-flag:hover:not(.active) {
            background: rgba(255,255,255,0.25);
            transform: scale(1.12);
        }
        .container {
            max-width: 1400px;
            margin: 2.5rem auto;
            padding: 0 5%;
        }
        @media (max-width: 992px) {
            header {
                flex-direction: column;
                gap: 1.5rem;
                padding: 1.5rem 5%;
            }
            .nav-right {
                justify-content: center;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <i class="fas fa-shield-halved"></i>
        <?= htmlspecialchars($t['title']) ?>
    </div>

    <div class="nav-right">
        <!-- Прапори мов (перемикання) -->
        <div class="lang-flags" title="<?= $t['lang_switch'] ?>">
            <div class="lang-flag <?= $current_lang === 'ua' ? 'active' : '' ?>" 
                 onclick="window.location='?lang=ua'">🇺🇦</div>
            <div class="lang-flag <?= $current_lang === 'en' ? 'active' : '' ?>" 
                 onclick="window.location='?lang=en'">EN</div>
            <div class="lang-flag <?= $current_lang === 'no' ? 'active' : '' ?>" 
                 onclick="window.location='?lang=no'">🇳🇴</div>
        </div>

        <!-- Навігація -->
        <nav>
            <a href="/index.php"><i class="fas fa-home"></i> <?= $t['nav_home'] ?></a>
            <a href="/admin/users.php"><i class="fas fa-users"></i> <?= $t['nav_users'] ?></a>
            <a href="/admin/ads.php"><i class="fas fa-bullhorn"></i> <?= $t['nav_ads'] ?></a>
            <a href="/admin/news.php"><i class="fas fa-newspaper"></i> <?= $t['nav_news'] ?></a>
            <a href="/admin/logout.php" style="color:#f87171;"><i class="fas fa-sign-out-alt"></i> <?= $t['nav_logout'] ?></a>
        </nav>
    </div>
</header>

<div class="container">