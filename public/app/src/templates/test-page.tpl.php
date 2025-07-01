<?php

$title = $title ?? 'Тестовая страница';
$heading = $heading ?? 'Заголовок по умолчанию';
$message = $message ?? 'Привет, мир!';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
</head>
<body>
    <h1><?= $heading ?></h1>
    <p><?= $message ?></p>
</body>
</html>
