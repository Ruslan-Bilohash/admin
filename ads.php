<?php
session_start();

// Тільки адмінська перевірка
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /admin/login.php");
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
$page_title = 'Оголошення';
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';

$stmt = $pdo->query("SELECT a.id, a.title, a.status, u.name as user_name, a.created_at 
                     FROM ads a 
                     LEFT JOIN users u ON a.user_id = u.id 
                     ORDER BY a.created_at DESC");
$ads = $stmt->fetchAll();
?>

<h1>Список оголошень</h1>

<table style="width:100%; border-collapse:collapse; background:white; border-radius:12px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
    <thead style="background:#1e293b; color:white;">
        <tr>
            <th style="padding:1rem;">ID</th>
            <th style="padding:1rem;">Заголовок</th>
            <th style="padding:1rem;">Автор</th>
            <th style="padding:1rem;">Статус</th>
            <th style="padding:1rem;">Дата</th>
            <th style="padding:1rem;">Дії</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ads as $ad): ?>
        <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:1rem; text-align:center;"><?= $ad['id'] ?></td>
            <td style="padding:1rem;"><?= htmlspecialchars($ad['title']) ?></td>
            <td style="padding:1rem;"><?= htmlspecialchars($ad['user_name'] ?? '—') ?></td>
            <td style="padding:1rem;"><?= htmlspecialchars($ad['status']) ?></td>
            <td style="padding:1rem;"><?= date('d.m.Y H:i', strtotime($ad['created_at'])) ?></td>
            <td style="padding:1rem; text-align:center;">
                <a href="/admin/ad_edit.php?id=<?= $ad['id'] ?>" style="color:#3b82f6;"><i class="fas fa-edit"></i></a>
                <a href="?delete_ad=<?= $ad['id'] ?>" onclick="return confirm('Видалити оголошення?')" style="color:#ef4444; margin-left:1rem;"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php'; ?>