<?php
// /admin/logout.php — Выход из админки
session_start();
session_destroy();
header("Location: /admin/login.php");
exit;