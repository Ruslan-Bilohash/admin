<?php
// /admin/news_edit.php — Редагування новини (для адміністратора)
// Оновлено: 02 січня 2026 року
// Доступно тільки для ролей admin/superadmin

session_start();

// Перевірка адмін-доступу (залежно від твоєї системи — варіант 1)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /admin/login.php");
    exit;
}

// Альтернативна перевірка через роль користувача (якщо використовуєш одну сесію)
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id > 0) {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if (!$user || !in_array($user['role'], ['admin', 'superadmin'])) {
        header("Location: /admin/login.php?error=no_rights");
        exit;
    }
}

$page_title = 'Редагування новини';

$news_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($news_id <= 0) {
    die("Невірний ID новини");
}

// Завантажуємо новину
try {
    $stmt = $pdo->prepare("
        SELECT id, title_ua, title_en, title_no, description, content, event_date, location, photo, status, created_at
        FROM news
        WHERE id = ?
    ");
    $stmt->execute([$news_id]);
    $news = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$news) {
        die("Новину не знайдено");
    }
} catch (PDOException $e) {
    error_log("Помилка завантаження новини: " . $e->getMessage());
    die("Помилка бази даних");
}

// Обробка форми редагування
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title_ua   = trim($_POST['title_ua'] ?? '');
    $title_en   = trim($_POST['title_en'] ?? '');
    $title_no   = trim($_POST['title_no'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content    = trim($_POST['content'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    $location   = trim($_POST['location'] ?? '');
    $status     = $_POST['status'] ?? 'draft';

    // Валідація
    if (empty($title_ua)) {
        $error = 'Вкажіть заголовок українською';
    } elseif (empty($description)) {
        $error = 'Додайте опис новини';
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE news 
                SET title_ua    = ?,
                    title_en    = ?,
                    title_no    = ?,
                    description = ?,
                    content     = ?,
                    event_date  = ?,
                    location    = ?,
                    status      = ?,
                    updated_at  = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $title_ua,
                $title_en,
                $title_no,
                $description,
                $content,
                $event_date ?: null,
                $location,
                $status,
                $news_id
            ]);

            $success = 'Новину успішно оновлено!';
        } catch (PDOException $e) {
            error_log("Помилка оновлення новини: " . $e->getMessage());
            $error = 'Не вдалося зберегти зміни';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> — MapsMe Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin:0; font-family:'Manrope',sans-serif; background:#f1f5f9; color:#1e293b; }
        header { background:#1e293b; color:white; padding:1rem 5%; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:1000; box-shadow:0 2px 10px rgba(0,0,0,0.3); }
        .logo { font-size:1.6rem; font-weight:700; }
        nav a { color:#cbd5e1; margin-left:1.8rem; text-decoration:none; font-weight:500; transition:0.2s; }
        nav a:hover { color:#60a5fa; }
        .container { max-width:1200px; margin:2rem auto; padding:0 5%; }
        h1 { margin-top:0; }
        .form-group { margin-bottom:1.8rem; }
        label { display:block; margin-bottom:0.5rem; font-weight:500; color:#555; }
        input, textarea, select {
            width:100%;
            padding:0.9rem;
            border:1px solid #ddd;
            border-radius:8px;
            font-size:1rem;
        }
        textarea { min-height:180px; resize:vertical; }
        input:focus, textarea:focus, select:focus { border-color:#4361ee; outline:none; box-shadow:0 0 0 3px rgba(67,97,238,0.2); }
        .btn { 
            padding:1rem 2rem; 
            background:#4361ee; 
            color:white; 
            border:none; 
            border-radius:8px; 
            font-size:1.1rem; 
            cursor:pointer; 
            transition:0.2s; 
        }
        .btn:hover { background:#3b82f6; transform:translateY(-2px); }
        .error, .success { padding:1rem; border-radius:8px; margin-bottom:1.5rem; text-align:center; }
        .error { background:#fee2e2; color:#991b1b; }
        .success { background:#d1fae5; color:#065f46; }
    </style>
</head>
<body>

<header>
    <div class="logo"><i class="fas fa-shield-halved"></i> MapsMe Admin</div>
    <nav>
        <a href="/admin/index.php">Головна</a>
        <a href="/admin/news.php">Список новин</a>
        <a href="/admin/logout.php">Вийти</a>
    </nav>
</header>

<div class="container">
    <h1>Редагування новини #<?= $news_id ?></h1>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="title_ua">Заголовок (українською) *</label>
            <input type="text" id="title_ua" name="title_ua" required value="<?= htmlspecialchars($news['title_ua']) ?>">
        </div>

        <div class="form-group">
            <label for="title_en">Title (English)</label>
            <input type="text" id="title_en" name="title_en" value="<?= htmlspecialchars($news['title_en']) ?>">
        </div>

        <div class="form-group">
            <label for="title_no">Tittel (Norsk)</label>
            <input type="text" id="title_no" name="title_no" value="<?= htmlspecialchars($news['title_no']) ?>">
        </div>

        <div class="form-group">
            <label for="description">Короткий опис *</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($news['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="content">Повний текст новини</label>
            <textarea id="content" name="content"><?= htmlspecialchars($news['content']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="event_date">Дата події (якщо є)</label>
            <input type="date" id="event_date" name="event_date" value="<?= htmlspecialchars($news['event_date'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="location">Місце / місто</label>
            <input type="text" id="location" name="location" value="<?= htmlspecialchars($news['location'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="status">Статус</label>
            <select id="status" name="status">
                <option value="draft"    <?= $news['status'] === 'draft'    ? 'selected' : '' ?>>Чернетка</option>
                <option value="published"<?= $news['status'] === 'published'? 'selected' : '' ?>>Опубліковано</option>
                <option value="archived" <?= $news['status'] === 'archived' ? 'selected' : '' ?>>Архівовано</option>
            </select>
        </div>

        <button type="submit" class="btn">Зберегти зміни</button>
    </form>
</div>

</body>
</html>