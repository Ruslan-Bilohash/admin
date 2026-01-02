<?php
// admin/login.php
// Адаптивний, сучасний вхід в адмін-панель
// Оновлено: січень 2026
// Рекомендації: замінити жорсткий логін/пароль на хешування + базу даних

session_start();

// Якщо вже авторизовані — перенаправляємо
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: /admin/index.php");
    exit;
}

$error = '';
$attempts_key = 'login_attempts_' . md5($_SERVER['REMOTE_ADDR']);
$max_attempts = 5;
$lockout_time = 900; // 15 хвилин

// Перевірка блокування
if (isset($_SESSION[$attempts_key])) {
    $data = $_SESSION[$attempts_key];
    if ($data['count'] >= $max_attempts && time() - $data['time'] < $lockout_time) {
        $error = 'Занадто багато невдалих спроб. Спробуйте знову через ' . 
                 ceil(($lockout_time - (time() - $data['time'])) / 60) . 
                 ' хвилин.';
    }
}

$admin_login = 'admin';           // ← змініть!
$admin_password_hash = password_hash('12345', PASSWORD_ARGON2ID); // ← змініть пароль!

// В реальному проекті використовуйте базу даних + password_hash()
// $admin_password_hash = '$argon2id$v=19$m=65536,t=4,p=1$...';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    
    $input_login    = trim($_POST['login']    ?? '');
    $input_password = $_POST['password'] ?? '';

    // Захист від brute-force — лічильник спроб
    if (!isset($_SESSION[$attempts_key])) {
        $_SESSION[$attempts_key] = ['count' => 0, 'time' => time()];
    }

    if (empty($input_login) || empty($input_login)) {
        $error = 'Заповніть логін та пароль';
    } 
    elseif ($input_login === $admin_login && password_verify($input_password, $admin_password_hash)) {
        // Успішний вхід
        $_SESSION['admin_logged_in']    = true;
        $_SESSION['admin_name']         = 'Адміністратор';
        $_SESSION['admin_login_time']   = time();
        $_SESSION['admin_ip']           = $_SERVER['REMOTE_ADDR'];

        // Скидаємо лічильник невдалих спроб
        unset($_SESSION[$attempts_key]);

        header("Location: /admin/index.php");
        exit;
    } 
    else {
        // Невдалий вхід
        $_SESSION[$attempts_key]['count']++;
        $_SESSION[$attempts_key]['time'] = time();

        $remaining = $max_attempts - $_SESSION[$attempts_key]['count'];
        $error = "Невірний логін або пароль. Залишилось спроб: " . max(0, $remaining);
        
        if ($_SESSION[$attempts_key]['count'] >= $max_attempts) {
            $error = "Акаунт тимчасово заблоковано на 15 хвилин через багато невдалих спроб.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Вхід · MapsMe Norway Admin</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --primary: #60a5fa;
            --primary-dark: #3b82f6;
            --danger: #f87171;
            --text: #f1f5f9;
            --text-secondary: #94a3b8;
            --transition: 0.25s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100dvh;
            font-family: 'Manrope', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(96,165,250,0.08) 0%, transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(96,165,250,0.06) 0%, transparent 35%);
            display: grid;
            place-items: center;
            padding: 1.5rem;
        }

        .login-card {
            background: var(--card);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 16px;
            border: 1px solid rgba(96,165,250,0.12);
            padding: clamp(1.75rem, 5vw, 3rem) clamp(1.5rem, 5vw, 2.5rem);
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 70px rgba(0,0,0,0.45),
                        inset 0 0 0 1px rgba(96,165,250,0.08);
            position: relative;
            overflow: hidden;
        }

        .logo {
            font-size: clamp(3.2rem, 12vw, 4.8rem);
            color: var(--primary);
            text-align: center;
            margin-bottom: 1.25rem;
            filter: drop-shadow(0 4px 12px rgba(96,165,250,0.35));
        }

        h1 {
            text-align: center;
            font-size: clamp(1.5rem, 5vw, 1.85rem);
            font-weight: 600;
            margin-bottom: 2rem;
            color: var(--text);
        }

        .error {
            background: rgba(248,113,113,0.15);
            color: #fca5a5;
            border: 1px solid rgba(248,113,113,0.3);
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.75rem;
            font-size: 0.95rem;
            text-align: center;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 1.05rem 1rem 1.05rem 3.1rem;
            border: none;
            border-radius: 10px;
            background: #334155;
            color: white;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group input:focus {
            outline: none;
            background: #374151;
            box-shadow: 0 0 0 3px rgba(96,165,250,0.35);
        }

        .input-icon {
            position: absolute;
            left: 1.1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1.25rem;
            pointer-events: none;
        }

        .btn-login {
            width: 100%;
            padding: 1.1rem;
            margin-top: 0.5rem;
            background: linear-gradient(90deg, var(--primary-dark), var(--primary));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59,130,246,0.35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .footer-text {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
                border-radius: 12px;
            }
            .logo { font-size: 3.8rem; }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo">
        <i class="fas fa-shield-halved"></i>
    </div>

    <h1>Вхід в адмін-панель</h1>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <div class="form-group">
            <i class="fas fa-user input-icon"></i>
            <input 
                type="text" 
                name="login" 
                placeholder="Логін" 
                required 
                autofocus 
                autocomplete="username"
                value="<?= htmlspecialchars($input_login ?? '') ?>">
        </div>

        <div class="form-group">
            <i class="fas fa-lock input-icon"></i>
            <input 
                type="password" 
                name="password" 
                placeholder="Пароль" 
                required 
                autocomplete="current-password">
        </div>

        <button type="submit" class="btn-login">
            Увійти
        </button>
    </form>

    <div class="footer-text">
        MapsMe Norway · © <?= date('Y') ?>
    </div>
</div>

</body>
</html>