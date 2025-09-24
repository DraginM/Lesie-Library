<?php
session_start();

$LOGIN = 'korgik13';
$PASSWORD = 'L1brariaN';

if ($_POST['login'] === $LOGIN && $_POST['password'] === $PASSWORD) {
    $_SESSION['authorized'] = true;
    header('Location: index.php');
    exit;
}

if ($_SESSION['authorized'] ?? false) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Вход в админку</title>
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/admin-login.css">
</head>
<body>
    <form method="POST">
        <h2>🔐 Вход для библиотекаря</h2>
        <input type="text" name="login" placeholder="Логин" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <div class="bottom">
            <a href="../index.html" class="btn secc">На главную</a>
            <button class="btn prime" type="submit">Войти</button>
        </div>
    </form>
</body>
</html>