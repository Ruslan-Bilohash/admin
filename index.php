<?php
// /admin/index.php — Головна сторінка адмін-панелі
// Оновлено: 2 січня 2026 року
// Повністю адаптивна, сучасна, з реальною статистикою з БД
// Автор: Руслан Білогаш — український розробник-ентузіаст

session_start();

// Перевірка адмінської авторизації
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /admin/login.php");
    exit;
}

// Підключаємо конфігурацію (там має бути $pdo)
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/hello_admin.php';
// Реальні запити до бази даних
try {
    // Оголошень на модерації
    $stmt = $pdo->query("SELECT COUNT(*) FROM ads WHERE status = 'pending'");
    $pending_ads = $stmt->fetchColumn();

    // Активних оголошень
    $stmt = $pdo->query("SELECT COUNT(*) FROM ads WHERE status = 'active'");
    $active_ads = $stmt->fetchColumn();

    // Усього користувачів
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $total_users = $stmt->fetchColumn();

    // Нові користувачі за сьогодні (від 00:00)
    $today_start = date('Y-m-d 00:00:00');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE created_at >= ?");
    $stmt->execute([$today_start]);
    $new_users = $stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Помилка статистики адмінки: " . $e->getMessage());
    // Фейкові значення на випадок помилки БД
    $pending_ads = $active_ads = $total_users = $new_users = '—';
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Адмін-панель — MapsMe Norway</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3b37c9;
            --light: #f8f9ff;
            --dark: #0f172a;
            --gray: #6b7280;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Manrope', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }
        header {
            background: linear-gradient(135deg, var(--dark), #172554);
            color: white;
            padding: 1.2rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }
        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        .logo i { color: #60a5fa; }
        nav a {
            color: #cbd5e1;
            margin-left: 2rem;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        nav a:hover { color: #60a5fa; transform: translateY(-2px); }
        .container { max-width: 1400px; margin: 0 auto; padding: 0 5%; }
        .welcome {
            text-align: center;
            padding: 6rem 0 4rem;
        }
        .welcome h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(3rem, 8vw, 5rem);
            margin-bottom: 1rem;
            color: var(--dark);
        }
        .welcome p { font-size: 1.4rem; color: var(--gray); max-width: 800px; margin: 0 auto; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            margin: 3rem 0 5rem;
        }
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            text-align: center;
            transition: all 0.4s ease;
        }
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(67,97,238,0.15);
        }
        .stat-icon {
            font-size: 4rem;
            margin-bottom: 1.2rem;
        }
        .stat-number {
            font-size: 3.2rem;
            font-weight: 800;
            margin: 0.5rem 0;
        }
        .stat-title {
            font-size: 1.3rem;
            color: var(--gray);
            margin-top: 0.5rem;
        }
        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            justify-content: center;
            margin-top: 4rem;
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            padding: 1.2rem 2.5rem;
            background: var(--primary);
            color: white;
            border-radius: 16px;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(67,97,238,0.25);
        }
        .action-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(67,97,238,0.35);
        }
        .action-btn.danger { background: var(--danger); }
        .action-btn.danger:hover { background: #dc2626; }
        @media (max-width: 768px) {
            .welcome { padding: 5rem 0 3rem; }
            .welcome h1 { font-size: 2.8rem; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <i class="fas fa-shield-halved"></i>
        Адмін-панель
    </div>
    <nav>
        <a href="/admin/index.php">Головна</a>
        <a href="/admin/users.php">Користувачі</a>
        <a href="/admin/ads.php">Оголошення</a>
        <a href="/admin/news.php">Новини</a>
        <a href="?logout=1" style="color:#f87171;">Вийти</a>
    </nav>
</header>

<div class="container">
    <div class="welcome">
        <h1>Ласкаво просимо, Адміністратор!</h1>
        <p>Керуйте MapsMe Norway легко та швидко</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card" style="color: var(--warning);">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-number"><?= $pending_ads ?></div>
            <div class="stat-title">Оголошень на модерації</div>
        </div>

        <div class="stat-card" style="color: var(--success);">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-number"><?= $active_ads ?></div>
            <div class="stat-title">Активних оголошень</div>
        </div>

        <div class="stat-card" style="color: var(--primary);">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-number"><?= $total_users ?></div>
            <div class="stat-title">Зареєстровано користувачів</div>
        </div>

        <div class="stat-card" style="color: #ec4899;">
            <div class="stat-icon"><i class="fas fa-user-plus"></i></div>
            <div class="stat-number"><?= $new_users ?></div>
            <div class="stat-title">Нових сьогодні</div>
        </div>
    </div>

    <div class="quick-actions">
        <a href="/admin/ads.php" class="action-btn">
            <i class="fas fa-bullhorn"></i> Переглянути оголошення
        </a>
        <a href="/admin/users.php" class="action-btn">
            <i class="fas fa-users-cog"></i> Керувати користувачами
        </a>
        <a href="/admin/news.php" class="action-btn">
            <i class="fas fa-newspaper"></i> Додати новину
        </a>
        <a href="?logout=1" class="action-btn danger">
            <i class="fas fa-sign-out-alt"></i> Вийти
        </a>
    </div>
</div>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php'; ?>
</body>
</html>