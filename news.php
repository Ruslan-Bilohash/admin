<?php
// /admin/news.php — Список новин з модерацією, повним текстом, фото та швидкою зміною статусу
// Оновлено: 2 січня 2026 року
// Автор: Руслан Білогаш
// ✅ Виправлено помилки з onclick, лапками та data-атрибутами
// ✅ Підтягує мініатюру фото + повний текст + велике фото в модалці
// ✅ AJAX-зміна статусу + анімація успіху
// ✅ Адаптивний дизайн, hover-ефекти, placeholder для фото

session_start();

// Перевірка адмін-доступу
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /admin/login.php");
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Обробка AJAX-запиту на зміну статусу
if (isset($_POST['action']) && $_POST['action'] === 'change_status' && isset($_POST['news_id']) && isset($_POST['new_status'])) {
    header('Content-Type: application/json');
    
    $news_id = (int)$_POST['news_id'];
    $new_status = in_array($_POST['new_status'], ['draft', 'moderation', 'published']) ? $_POST['new_status'] : 'draft';
    
    try {
        $stmt = $pdo->prepare("UPDATE news SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$new_status, $news_id]);
        
        echo json_encode(['success' => true, 'status' => $new_status]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Обробка видалення
if (isset($_GET['delete_news'])) {
    $delete_id = (int)$_GET['delete_news'];
    try {
        $stmt = $pdo->prepare("SELECT photo FROM news WHERE id = ?");
        $stmt->execute([$delete_id]);
        $photo = $stmt->fetchColumn();

        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$delete_id]);

        if ($photo && file_exists($_SERVER['DOCUMENT_ROOT'] . $photo)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $photo);
        }

        header("Location: /admin/news.php?status=deleted");
        exit;
    } catch (PDOException $e) {
        $error = "Помилка видалення: " . $e->getMessage();
    }
}

// Отримання списку новин
try {
    $stmt = $pdo->query("
        SELECT id, title_ua, 
               COALESCE(description_ua, content, 'Текст відсутній') AS description,
               photo, status, created_at
        FROM news
        ORDER BY created_at DESC
    ");
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Помилка завантаження списку: " . $e->getMessage();
    $news = [];
}

// Повідомлення
$status_msg = '';
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'saved':    $status_msg = '<div class="status success">Новину успішно збережено!</div>'; break;
        case 'deleted':  $status_msg = '<div class="status success">Новину видалено.</div>'; break;
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новини — Адмін-панель MapsMe Norway</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/admin/css/news.css">
</head>
<body>

<header>
    <div class="logo">Адмін-панель MapsMe Norway</div>
    <nav>
        <a href="/admin/"><i class="fas fa-home"></i> Головна</a>
        <a href="/admin/news.php"><i class="fas fa-newspaper"></i> Новини</a>
        <a href="?logout=1" style="color:#f87171;"><i class="fas fa-sign-out-alt"></i> Вийти</a>
    </nav>
</header>

<div class="container">
    <h1>Список новин</h1>

    <a href="/admin/add-news.php" class="add-btn">
        <i class="fas fa-plus"></i> Додати нову новину / подію
    </a>

    <?php if (isset($error)): ?>
        <div class="status error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?= $status_msg ?? '' ?>

    <?php if (empty($news)): ?>
        <div style="text-align:center; padding:4rem 0; color:#9ca3af; font-size:1.3rem;">
            Поки що немає жодної новини. Додайте першу!
        </div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Фото</th>
                <th>ID</th>
                <th>Заголовок (укр)</th>
                <th>Статус модерації</th>
                <th>Дата створення</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($news as $n): ?>
            <tr data-news-id="<?= $n['id'] ?>">
                <td data-label="Фото">
                    <?php if ($n['photo']): ?>
                        <img src="<?= htmlspecialchars($n['photo']) ?>" 
                             alt="Мініатюра" 
                             class="news-photo">
                    <?php else: ?>
                        <div class="no-photo">Немає фото</div>
                    <?php endif; ?>
                </td>
                <td data-label="ID"><?= $n['id'] ?></td>
                <td data-label="Заголовок"><?= htmlspecialchars($n['title_ua']) ?></td>
                <td data-label="Статус">
                    <select class="status-select" data-news-id="<?= $n['id'] ?>">
                        <option value="draft" <?= $n['status']==='draft' ? 'selected' : '' ?>>Чернетка</option>
                        <option value="moderation" <?= $n['status']==='moderation' ? 'selected' : '' ?>>На модерації</option>
                        <option value="published" <?= $n['status']==='published' ? 'selected' : '' ?>>Опубліковано</option>
                    </select>
                </td>
                <td data-label="Дата"><?= date('d.m.Y H:i', strtotime($n['created_at'])) ?></td>
                <td data-label="Дії" class="actions">
                    <a href="/admin/add-news.php?id=<?= $n['id'] ?>" class="edit" title="Редагувати">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="javascript:void(0)" 
                       class="view" 
                       title="Переглянути повний текст"
                       data-id="<?= $n['id'] ?>"
                       data-title="<?= htmlspecialchars(addslashes($n['title_ua'])) ?>"
                       data-content="<?= htmlspecialchars(addslashes($n['description'])) ?>"
                       data-photo="<?= htmlspecialchars($n['photo'] ?? '') ?>">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="?delete_news=<?= $n['id'] ?>"
                       onclick="return confirm('Ви дійсно хочете видалити цю новину?')"
                       class="delete" title="Видалити">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- Модальне вікно -->
<div id="newsModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">×</span>
        <h2 id="modalTitle"></h2>
        <div id="modalPhoto" style="margin-bottom:1.5rem; text-align:center;"></div>
        <div id="modalContent" style="line-height:1.7; font-size:1.1rem; color:#333;"></div>
    </div>
</div>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php'; ?>
<script src="/admin/js/news.js"></script>
</body>
</html>