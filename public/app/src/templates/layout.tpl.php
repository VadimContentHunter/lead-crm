<?php

$title = $title ?? 'Без названия';
$head = $head ?? '';
$content = $content ?? '';

$main_menu = $main_menu ?? '';
$content_container = $content_container ?? '';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <?= $head ?>
</head>
<body>
    <!-- Основная страница -->
    <div class="page-container">

        <?= $main_menu ?>

        <?= $content_container ?>
        
    </div>
</body>


</html>
