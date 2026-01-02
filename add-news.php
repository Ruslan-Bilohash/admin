<?php
// /admin/add-news.php — Додавання / редагування нової новини/події в адмінці
// Оновлено: 02 січня 2026 року
// Автор: Руслан Білогаш
// ✅ Виправлено помилку 500 при збереженні змін (?id=...)
// ✅ Редірект після успіху
// ✅ Повний підтяг даних при редагуванні
// ✅ Правильна обробка фото (оновлення + видалення старого)
// ✅ Переклади + Quill + CSRF + безпечний код

session_start();

// Перевірка адмін-доступу
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /admin/login.php");
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// =============================================
// ПОВНИЙ МАСИВ ПЕРЕКЛАДІВ (UA / EN / NO)
// =============================================
$translations = [
    'ua' => [
        'page_title_add' => 'Додати новину — Адмін-панель MapsMe Norway',
        'page_title_edit' => 'Редагувати новину — Адмін-панель MapsMe Norway',
        'header_title' => 'Адмін-панель MapsMe Norway',
        'nav_home' => 'На сайт',
        'nav_news' => 'Новини',
        'nav_logout' => 'Вийти',
        'h1_add' => 'Додати нову новину / подію',
        'h1_edit' => 'Редагувати новину / подію',
        'lang_ua' => '🇺🇦 Українська (обов’язково)',
        'lang_en_add' => '🇺🇸 + Додати англійську',
        'lang_no_add' => '🇳🇴 + Додати норвезьку',
        'lang_en_edit' => '🇺🇸 Редагувати англійську',
        'lang_no_edit' => '🇳🇴 Редагувати норвезьку',
        'lang_en_title' => 'Англійська версія',
        'lang_no_title' => 'Норвезька версія',
        'hide' => '× Сховати',
        'title_ua_label' => 'Заголовок українською ★ обов’язково',
        'title_en_label' => 'Заголовок англійською',
        'title_no_label' => 'Заголовок норвезькою',
        'content_ua_label' => 'Текст новини українською ★ обов’язково',
        'content_en_label' => 'Текст англійською',
        'content_no_label' => 'Текст норвезькою',
        'event_date_label' => 'Дата події',
        'location_label' => 'Місце / місто',
        'coords_label' => 'Координати події',
        'lat_label' => 'Широта (lat)',
        'lng_label' => 'Довгота (lng)',
        'photo_label' => 'Головне фото (jpg/png/webp, макс. 5MB)',
        'current_photo' => 'Поточне фото:',
        'submit_add' => 'Додати новину',
        'submit_edit' => 'Зберегти зміни',
        'cancel_btn' => 'Скасувати',
        'success_add' => 'Новину успішно додано!',
        'success_edit' => 'Новину успішно оновлено!',
        'success_link' => 'Перейти до списку новин →',
        'csrf_error' => 'Помилка безпеки (CSRF). Оновіть сторінку та спробуйте ще раз.',
        'required_title_ua' => 'Вкажіть заголовок українською (обов’язково)',
        'required_content_ua' => 'Додайте опис українською (обов’язково)',
        'photo_error_format' => 'Непідтримуваний формат або розмір фото (макс. 5MB, jpg/png/webp)',
        'photo_error_upload' => 'Не вдалося зберегти фото',
        'db_error' => 'Помилка бази даних',
    ],
    'en' => [
        'page_title_add' => 'Add News — MapsMe Norway Admin Panel',
        'page_title_edit' => 'Edit News — MapsMe Norway Admin Panel',
        'header_title' => 'MapsMe Norway Admin Panel',
        'nav_home' => 'Back to Site',
        'nav_news' => 'News',
        'nav_logout' => 'Logout',
        'h1_add' => 'Add New News / Event',
        'h1_edit' => 'Edit News / Event',
        'lang_ua' => '🇺🇦 Ukrainian (required)',
        'lang_en_add' => '🇺🇸 + Add English version',
        'lang_no_add' => '🇳🇴 + Add Norwegian version',
        'lang_en_edit' => '🇺🇸 Edit English version',
        'lang_no_edit' => '🇳🇴 Edit Norwegian version',
        'lang_en_title' => 'English version',
        'lang_no_title' => 'Norwegian version',
        'hide' => '× Hide',
        'title_ua_label' => 'Title in Ukrainian ★ required',
        'title_en_label' => 'Title in English',
        'title_no_label' => 'Title in Norwegian',
        'content_ua_label' => 'News text in Ukrainian ★ required',
        'content_en_label' => 'News text in English',
        'content_no_label' => 'News text in Norwegian',
        'event_date_label' => 'Event date',
        'location_label' => 'Location / City',
        'coords_label' => 'Event coordinates',
        'lat_label' => 'Latitude (lat)',
        'lng_label' => 'Longitude (lng)',
        'photo_label' => 'Main news photo (jpg/png/webp, max 5MB)',
        'current_photo' => 'Current photo:',
        'submit_add' => 'Add News',
        'submit_edit' => 'Save Changes',
        'cancel_btn' => 'Cancel',
        'success_add' => 'News successfully added!',
        'success_edit' => 'News successfully updated!',
        'success_link' => 'Go to news list →',
        'csrf_error' => 'Security error (CSRF). Refresh the page and try again.',
        'required_title_ua' => 'Please enter the title in Ukrainian (required)',
        'required_content_ua' => 'Please add the description in Ukrainian (required)',
        'photo_error_format' => 'Unsupported format or photo size (max 5MB, jpg/png/webp)',
        'photo_error_upload' => 'Failed to save photo',
        'db_error' => 'Database error',
    ],
    'no' => [
        'page_title_add' => 'Legg til nyhet — MapsMe Norway Admin Panel',
        'page_title_edit' => 'Rediger nyhet — MapsMe Norway Admin Panel',
        'header_title' => 'MapsMe Norway Admin Panel',
        'nav_home' => 'Tilbake til nettstedet',
        'nav_news' => 'Nyheter',
        'nav_logout' => 'Logg ut',
        'h1_add' => 'Legg til ny nyhet / hendelse',
        'h1_edit' => 'Rediger nyhet / hendelse',
        'lang_ua' => '🇺🇦 Ukrainsk (påkrevd)',
        'lang_en_add' => '🇺🇸 + Legg til engelsk versjon',
        'lang_no_add' => '🇳🇴 + Legg til norsk versjon',
        'lang_en_edit' => '🇺🇸 Rediger engelsk versjon',
        'lang_no_edit' => '🇳🇴 Rediger norsk versjon',
        'lang_en_title' => 'Engelsk versjon',
        'lang_no_title' => 'Norsk versjon',
        'hide' => '× Skjul',
        'title_ua_label' => 'Tittel på ukrainsk ★ påkrevd',
        'title_en_label' => 'Tittel på engelsk',
        'title_no_label' => 'Tittel på norsk',
        'content_ua_label' => 'Nyhetstekst på ukrainsk ★ påkrevd',
        'content_en_label' => 'Nyhetstekst på engelsk',
        'content_no_label' => 'Nyhetstekst på norsk',
        'event_date_label' => 'Hendelsesdato',
        'location_label' => 'Sted / by',
        'coords_label' => 'Koordinater for hendelse',
        'lat_label' => 'Breddegrad (lat)',
        'lng_label' => 'Lengdegrad (lng)',
        'photo_label' => 'Hovedbilde for nyhet (jpg/png/webp, maks 5MB)',
        'current_photo' => 'Gjeldende bilde:',
        'submit_add' => 'Legg til nyhet',
        'submit_edit' => 'Lagre endringer',
        'cancel_btn' => 'Avbryt',
        'success_add' => 'Nyhet ble lagt til!',
        'success_edit' => 'Nyhet ble oppdatert!',
        'success_link' => 'Gå til nyhetsliste →',
        'csrf_error' => 'Sikkerhetsfeil (CSRF). Oppdater siden og prøv igjen.',
        'required_title_ua' => 'Vennligst skriv inn tittel på ukrainsk (påkrevd)',
        'required_content_ua' => 'Vennligst legg til beskrivelse på ukrainsk (påkrevd)',
        'photo_error_format' => 'Ustøttet format eller bildestørrelse (maks 5MB, jpg/png/webp)',
        'photo_error_upload' => 'Kunne ikke lagre bilde',
        'db_error' => 'Databasefeil',
    ]
];

// Обираємо переклади
$current_lang = $_SESSION['lang'] ?? 'ua';
$t = $translations[$current_lang] ?? $translations['ua'];

// =============================================
// CSRF-захист (виправлено помилку undefined + TypeError)
// =============================================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// =============================================
// ЛОГІКА РЕДАГУВАННЯ / СТВОРЕННЯ
// =============================================
$news_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$title_ua = $title_en = $title_no = '';
$desc_ua = $desc_en = $desc_no = '';
$event_date = $location = '';
$lat = $lng = '';
$photo = '';
$submit_text = $t['submit_add'];
$h1_text = $t['h1_add'];

if ($news_id > 0) {
    try {
        $stmt = $pdo->prepare("
            SELECT title_ua, title_en, title_no,
                   description_ua, description_en, description_no,
                   event_date, location, lat, lng, photo
            FROM news
            WHERE id = ?
        ");
        $stmt->execute([$news_id]);
        $news = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($news) {
            $title_ua = $news['title_ua'] ?? '';
            $title_en = $news['title_en'] ?? '';
            $title_no = $news['title_no'] ?? '';
            $desc_ua = $news['description_ua'] ?? '';
            $desc_en = $news['description_en'] ?? '';
            $desc_no = $news['description_no'] ?? '';
            $event_date = $news['event_date'] ?? '';
            $location = $news['location'] ?? '';
            $lat = $news['lat'] ?? '';
            $lng = $news['lng'] ?? '';
            $photo = $news['photo'] ?? '';
            $submit_text = $t['submit_edit'];
            $h1_text = $t['h1_edit'];
        }
    } catch (PDOException $e) {
        $error = $t['db_error'] . ': ' . htmlspecialchars($e->getMessage());
    }
}

// =============================================
// ОБРОБКА ФОРМИ (з повним захистом від помилок 500)
// =============================================
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Перевірка CSRF (безпечна, не ламає якщо токен відсутній)
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = $t['csrf_error'];
    } else {
        // Очищаємо токен після використання
        unset($_SESSION['csrf_token']);

        $title_ua   = trim($_POST['title_ua'] ?? '');
        $title_en   = trim($_POST['title_en'] ?? '');
        $title_no   = trim($_POST['title_no'] ?? '');
        $desc_ua    = trim($_POST['description_ua'] ?? '');
        $desc_en    = trim($_POST['description_en'] ?? '');
        $desc_no    = trim($_POST['description_no'] ?? '');
        $event_date = trim($_POST['event_date'] ?? '');
        $location   = trim($_POST['location'] ?? '');
        $lat        = !empty($_POST['lat']) ? (float)$_POST['lat'] : null;
        $lng        = !empty($_POST['lng']) ? (float)$_POST['lng'] : null;

        if (empty($title_ua)) {
            $error = $t['required_title_ua'];
        } elseif (empty($desc_ua)) {
            $error = $t['required_content_ua'];
        } else {
            try {
                $new_photo = $photo; // зберігаємо старе фото

                // Обробка завантаження нового фото
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/news/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    if (in_array($ext, $allowed) && $_FILES['photo']['size'] < 5 * 1024 * 1024) {
                        $photo_name = 'news_' . time() . '_' . uniqid() . '.' . $ext;
                        $photo_path = $upload_dir . $photo_name;
                        if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                            $new_photo = '/uploads/news/' . $photo_name;
                            // Видаляємо старе фото
                            if ($photo && file_exists($_SERVER['DOCUMENT_ROOT'] . $photo)) {
                                @unlink($_SERVER['DOCUMENT_ROOT'] . $photo);
                            }
                        } else {
                            $error = $t['photo_error_upload'];
                        }
                    } else {
                        $error = $t['photo_error_format'];
                    }
                }

                if (!$error) {
                    if ($news_id > 0) {
                        // Оновлення новини
                        $stmt = $pdo->prepare("
                            UPDATE news SET
                                title_ua = ?,
                                title_en = ?,
                                title_no = ?,
                                description_ua = ?,
                                description_en = ?,
                                description_no = ?,
                                event_date = ?,
                                location = ?,
                                lat = ?,
                                lng = ?,
                                photo = ?,
                                updated_at = NOW()
                            WHERE id = ?
                        ");
                        $stmt->execute([
                            $title_ua, $title_en, $title_no,
                            $desc_ua, $desc_en, $desc_no,
                            $event_date ?: null, $location,
                            $lat, $lng, $new_photo, $news_id
                        ]);
                        $success = $t['success_edit'];
                    } else {
                        // Додавання нової новини
                        $stmt = $pdo->prepare("
                            INSERT INTO news
                            (title_ua, title_en, title_no,
                             description_ua, description_en, description_no,
                             event_date, location, lat, lng, photo, status, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', NOW())
                        ");
                        $stmt->execute([
                            $title_ua, $title_en, $title_no,
                            $desc_ua, $desc_en, $desc_no,
                            $event_date ?: null, $location,
                            $lat, $lng, $new_photo
                        ]);
                        $success = $t['success_add'];
                    }

                    // Редірект після успіху (запобігає повторному POST)
                    header("Location: /admin/news.php?status=saved");
                    exit;
                }
            } catch (PDOException $e) {
                error_log("Помилка збереження новини: " . $e->getMessage());
                $error = $t['db_error'] . ': ' . htmlspecialchars($e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($t['page_title_' . ($news_id > 0 ? 'edit' : 'add')]) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        /* Твій попередній стиль — нічого не змінюю, тільки додаю */
        :root {
            --primary: #4361ee;
            --dark: #0f172a;
            --gray: #6b7280;
            --light: #f8f9ff;
            --success: #10b981;
            --danger: #ef4444;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            background: var(--light);
            color: var(--dark);
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .logo i { font-size: 1.8rem; margin-right: 0.8rem; }
        nav a { color: white; margin-left: 1.5rem; text-decoration: none; font-weight: 500; }
        nav a:hover { color: #60a5fa; }
        .container { max-width: 1100px; margin: 2rem auto; padding: 0 5%; }
        h1 { font-family: 'Playfair Display', serif; font-size: 3rem; margin: 0 0 2rem; text-align: center; }
        .form-group { margin-bottom: 2rem; }
        .form-group label { display: block; margin-bottom: 0.6rem; font-weight: 600; color: #374151; }
        input, textarea { width: 100%; padding: 1rem; border: 1px solid #d1d5db; border-radius: 10px; font-size: 1.1rem; }
        input:focus, textarea:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(67,97,238,0.2); }
        textarea { min-height: 160px; resize: vertical; }
        .lang-controls { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 2.5rem; justify-content: center; }
        .lang-btn { padding: 0.9rem 1.8rem; border-radius: 12px; cursor: pointer; font-weight: 600; border: none; transition: all 0.3s; font-size: 1rem; }
        .lang-btn.active { background: var(--primary); color: white; box-shadow: 0 4px 15px rgba(67,97,238,0.3); }
        .lang-btn.secondary { background: #f8fafc; color: #6b7280; border: 2px solid #e2e8f0; }
        .lang-btn.secondary:hover { background: white; border-color: var(--primary); color: var(--primary); transform: translateY(-2px); }
        .lang-section { display: none; margin-bottom: 2.5rem; padding: 2rem; background: white; border-radius: 16px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); border: 1px solid #f1f5f9; }
        .lang-section.active { display: block; }
        .lang-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #f1f5f9; }
        .lang-header h3 { margin: 0; font-size: 1.4rem; color: var(--primary); font-weight: 700; }
        .hide-lang { background: none; border: none; color: var(--danger); font-size: 1.5rem; cursor: pointer; padding: 0.5rem; border-radius: 50%; transition: all 0.2s; }
        .hide-lang:hover { background: #fee2e2; transform: scale(1.1); }
        .quill-editor { min-height: 250px; background: white; border-radius: 10px; }
        .btn-group { display: flex; gap: 1.5rem; justify-content: center; margin-top: 3rem; flex-wrap: wrap; }
        .btn { padding: 1.2rem 2.5rem; border: none; border-radius: 12px; font-size: 1.15rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s; color: white; }
        .btn-primary { background: var(--primary); }
        .btn-primary:hover { background: #3b82f6; transform: translateY(-3px); }
        .btn-secondary { background: var(--gray); }
        .btn-secondary:hover { background: #4b5563; transform: translateY(-3px); }
        .message { padding: 1.3rem; border-radius: 12px; margin-bottom: 2rem; text-align: center; font-size: 1.15rem; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .success { background: linear-gradient(90deg, #d1fae5, #a7f3d0); color: #065f46; border: 1px solid #86efac; }
        .success a { color: #059669; text-decoration: none; font-weight: 700; }
        .success a:hover { text-decoration: underline; }
        .coords-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .preview-photo { max-width: 300px; margin-top: 1rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        @media (max-width: 768px) {
            .lang-controls { justify-content: center; }
            .coords-grid { grid-template-columns: 1fr; }
            h1 { font-size: 2.2rem; }
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <i class="fas fa-shield-halved"></i> <?= htmlspecialchars($t['header_title']) ?>
    </div>
    <nav>
        <a href="/index.php"><i class="fas fa-home"></i> <?= $t['nav_home'] ?></a>
        <a href="/admin/news.php"><i class="fas fa-newspaper"></i> <?= $t['nav_news'] ?></a>
        <a href="?logout=1" style="color:#f87171;"><i class="fas fa-sign-out-alt"></i> <?= $t['nav_logout'] ?></a>
    </nav>
</header>
<div class="container">
    <h1><?= htmlspecialchars($h1_text) ?></h1>
    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <input type="hidden" name="id" value="<?= $news_id ?>">
        <!-- Кнопки перемикання мов -->
        <div class="lang-controls">
            <button type="button" class="lang-btn active" data-lang="ua">
                <?= $t['lang_ua'] ?>
            </button>
            <button type="button" class="lang-btn secondary" data-lang="en">
                <?= $t['lang_en_add'] ?>
            </button>
            <button type="button" class="lang-btn secondary" data-lang="no">
                <?= $t['lang_no_add'] ?>
            </button>
        </div>
        <!-- Українська (завжди видима) -->
        <div class="lang-section active" data-lang="ua">
            <div class="form-group">
                <label for="title_ua"><?= $t['title_ua_label'] ?></label>
                <input type="text" id="title_ua" name="title_ua" value="<?= htmlspecialchars($title_ua) ?>" required maxlength="250">
            </div>
            <div class="form-group">
                <label for="quill-ua"><?= $t['content_ua_label'] ?></label>
                <div id="quill-ua" class="quill-editor"></div>
                <textarea name="description_ua" id="description_ua" style="display:none;"><?= htmlspecialchars($desc_ua) ?></textarea>
            </div>
        </div>
        <!-- Англійська -->
        <div class="lang-section" data-lang="en">
            <div class="lang-header">
                <h3><?= $t['lang_en_title'] ?></h3>
                <button type="button" class="hide-lang" data-lang="en" title="Сховати">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="form-group">
                <label for="title_en"><?= $t['title_en_label'] ?></label>
                <input type="text" id="title_en" name="title_en" value="<?= htmlspecialchars($title_en) ?>" maxlength="250">
            </div>
            <div class="form-group">
                <label for="quill-en"><?= $t['content_en_label'] ?></label>
                <div id="quill-en" class="quill-editor"></div>
                <textarea name="description_en" id="description_en" style="display:none;"><?= htmlspecialchars($desc_en) ?></textarea>
            </div>
        </div>
        <!-- Норвезька -->
        <div class="lang-section" data-lang="no">
            <div class="lang-header">
                <h3><?= $t['lang_no_title'] ?></h3>
                <button type="button" class="hide-lang" data-lang="no" title="Сховати">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="form-group">
                <label for="title_no"><?= $t['title_no_label'] ?></label>
                <input type="text" id="title_no" name="title_no" value="<?= htmlspecialchars($title_no) ?>" maxlength="250">
            </div>
            <div class="form-group">
                <label for="quill-no"><?= $t['content_no_label'] ?></label>
                <div id="quill-no" class="quill-editor"></div>
                <textarea name="description_no" id="description_no" style="display:none;"><?= htmlspecialchars($desc_no) ?></textarea>
            </div>
        </div>
        <!-- Інші поля -->
        <div class="form-group">
            <label for="event_date"><?= $t['event_date_label'] ?></label>
            <input type="date" id="event_date" name="event_date" value="<?= htmlspecialchars($event_date) ?>">
        </div>
        <div class="form-group">
            <label for="location"><?= $t['location_label'] ?></label>
            <input type="text" id="location" name="location" value="<?= htmlspecialchars($location) ?>" placeholder="Oslo, Norway">
        </div>
        <div class="form-group">
            <label><?= $t['coords_label'] ?></label>
            <div class="coords-grid">
                <div>
                    <label for="lat"><?= $t['lat_label'] ?></label>
                    <input type="number" step="any" id="lat" name="lat" value="<?= htmlspecialchars($lat) ?>" placeholder="59.9139">
                </div>
                <div>
                    <label for="lng"><?= $t['lng_label'] ?></label>
                    <input type="number" step="any" id="lng" name="lng" value="<?= htmlspecialchars($lng) ?>" placeholder="10.7522">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="photo"><?= $t['photo_label'] ?></label>
            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
            <?php if ($photo): ?>
                <div>
                    <p style="margin-top: 0.8rem; color: #6b7280;"><?= $t['current_photo'] ?></p>
                    <img src="<?= htmlspecialchars($photo) ?>" alt="Поточне фото" class="preview-photo">
                </div>
            <?php endif; ?>
        </div>
        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $submit_text ?>
            </button>
            <a href="/admin/news.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> <?= $t['cancel_btn'] ?>
            </a>
        </div>
    </form>
</div>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quillConfig = {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['link', 'image'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean']
            ]
        }
    };
    // Ініціалізація Quill для кожної мови
    const quills = {};
    ['ua', 'en', 'no'].forEach(lang => {
        const quill = new Quill(`#quill-${lang}`, quillConfig);
        quills[lang] = quill;
      
        const textarea = document.getElementById(`description_${lang}`);
        if (textarea) {
            quill.root.innerHTML = textarea.value || '';
          
            quill.on('text-change', () => {
                textarea.value = quill.root.innerHTML;
            });
        }
    });
    // Перемикання мов
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const lang = this.dataset.lang;
            const section = document.querySelector(`.lang-section[data-lang="${lang}"]`);
            if (lang === 'ua') return;
            if (section.classList.contains('active')) {
                section.classList.remove('active');
                this.innerHTML = lang === 'en' ? '🇺🇸 + Додати англійську' : '🇳🇴 + Додати норвезьку';
                this.classList.remove('active');
                this.classList.add('secondary');
            } else {
                section.classList.add('active');
                this.innerHTML = lang === 'en' ? '🇺🇸 Редагувати англійську' : '🇳🇴 Редагувати норвезьку';
                this.classList.remove('secondary');
                this.classList.add('active');
            }
        });
    });
    document.querySelectorAll('.hide-lang').forEach(btn => {
        btn.addEventListener('click', function() {
            const lang = this.dataset.lang;
            document.querySelector(`.lang-section[data-lang="${lang}"]`).classList.remove('active');
            const toggle = document.querySelector(`.lang-btn[data-lang="${lang}"]`);
            toggle.innerHTML = lang === 'en' ? '🇺🇸 + Додати англійську' : '🇳🇴 + Додати норвезьку';
            toggle.classList.remove('active');
            toggle.classList.add('secondary');
        });
    });
    // Автофокус на першому полі
    document.getElementById('title_ua').focus();
});
</script>
</body>
</html>