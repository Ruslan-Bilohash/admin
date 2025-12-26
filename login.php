<?php
// admin/login.php
// Спокойное, стильное оформление входа — тёмная тема с мягким градиентом
// Дата: 26 декабря 2025

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Уже авторизован? → на главную
if (isAdmin()) {
    header("Location: /admin/index.php");
    exit;
}

// Мета-теги
$_SESSION['meta'] = [
    'title' => 'Вход в админ-панель — Pro Website',
    'description' => 'Безопасный вход в панель управления сайтом и магазином',
    'keywords' => 'админ панель, вход, управление сайтом'
];

// Обработка формы
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Заполните все поля";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin'] = true;
            header("Location: /admin/index.php");
            exit;
        } else {
            $error = "Неверный логин или пароль";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($_SESSION['meta']['title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($_SESSION['meta']['description']); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($_SESSION['meta']['keywords']); ?>">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e1e2f 0%, #2a2a40 50%, #1e1e2f 100%);
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: #e0e0ff;
            overflow: hidden;
            position: relative;
        }

        /* Мягкие снежинки (25–26 декабря) */
        .snow-container {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 1;
        }
        .snowflake {
            position: absolute;
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
            animation: fall linear infinite;
            user-select: none;
        }
        @keyframes fall {
            0% { transform: translateY(-100vh) rotate(0deg); opacity: 0.7; }
            100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
        }

        .login-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: rgba(40, 40, 60, 0.75);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 440px;
            transition: transform 0.4s ease;
        }

        .login-card:hover {
            transform: translateY(-8px);
        }

        .logo {
            font-size: 2.8rem;
            font-weight: 700;
            background: linear-gradient(90deg, #6c5ce7, #a29bfe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 2rem;
        }

        h2 {
            color: #a29bfe;
            text-align: center;
            margin-bottom: 2.2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .form-control {
            background: rgba(60, 60, 80, 0.6);
            border: 1px solid rgba(162, 155, 254, 0.3);
            color: #f0f0ff;
            border-radius: 12px;
            padding: 0.85rem 1.4rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(60, 60, 80, 0.85);
            border-color: #a29bfe;
            box-shadow: 0 0 0 0.25rem rgba(162, 155, 254, 0.25);
            color: #ffffff;
        }

        .form-label {
            color: #c7c7ff;
            font-weight: 500;
            margin-bottom: 0.6rem;
        }

        .btn-login {
            background: linear-gradient(90deg, #6c5ce7, #a29bfe);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.95rem;
            border-radius: 50px;
            transition: all 0.4s ease;
            box-shadow: 0 6px 20px rgba(108,92,231,0.35);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(108,92,231,0.5);
        }

        .error-alert {
            background: rgba(220, 53, 69, 0.15);
            border: 1px solid rgba(220, 53, 69, 0.4);
            color: #ffcccc;
            border-radius: 12px;
            padding: 1.1rem;
            margin-bottom: 1.8rem;
            font-size: 0.95rem;
        }

        @media (max-width: 576px) {
            .login-card { padding: 2rem 1.8rem; }
            .logo { font-size: 2.3rem; }
        }
    </style>
</head>
<body>

    <!-- Снежинки (25–26 декабря) -->
    <div class="snow-container" id="snow"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="logo">PRO WEBSITE</div>
            <h2>Вход в админ-панель</h2>

            <?php if ($error): ?>
                <div class="error-alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="on">
                <div class="mb-4">
                    <label class="form-label">Логин</label>
                    <input type="text" name="username" class="form-control" 
                           value="admin" required autocomplete="username" placeholder="Введите логин">
                </div>

                <div class="mb-4">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control" 
                           required autocomplete="current-password" placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i> Войти
                </button>
            </form>
        </div>
    </div>

    <script>
        // Снежинки (только 25–26 декабря 2025)
        const today = new Date();
        const month = today.getMonth() + 1;
        const day = today.getDate();
        if ((month === 12 && day >= 25 && day <= 26)) {
            const snow = document.getElementById('snow');
            for (let i = 0; i < 70; i++) {
                const flake = document.createElement('div');
                flake.className = 'snowflake';
                flake.innerHTML = Math.random() > 0.5 ? '❄' : '❅';
                flake.style.left = Math.random() * 100 + 'vw';
                flake.style.animationDuration = (Math.random() * 10 + 8) + 's';
                flake.style.animationDelay = Math.random() * 6 + 's';
                flake.style.opacity = Math.random() * 0.5 + 0.5;
                flake.style.fontSize = (Math.random() * 0.8 + 0.6) + 'rem';
                snow.appendChild(flake);
            }
        }
    </script>

    <!-- Bootstrap JS (для будущих улучшений) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>