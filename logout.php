<?php
session_start();
session_unset(); // Удаляем все переменные сессии
session_destroy(); // Уничтожаем сессию
header("Location: /admin/login.php");
exit;
?>