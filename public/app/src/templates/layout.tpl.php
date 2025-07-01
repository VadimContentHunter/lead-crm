<?php

$title = $title ?? 'Без названия';
$head = $head ?? '';
$content = $content ?? '';

$main_menu = $main_menu ?? '';
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


        <div class="content-container">
            <!-- Меню внутри контента -->
            <aside class="header-content">
                <nav class="menu-content main-wrapper-horizontal">
                    <button type="button" class="default-btn">Сохранить</button>
                    <button type="button" class="default-btn">Удалить</button>
                </nav>
            </aside>

            <!-- Основной контент -->
            <main class="main-content">
                <div></div>
            </main>

            <footer class="footer-content">
                    &copy; <?= date('Y') ?> CRM Обменка
            </footer>
        </div>
    </div>
</body>


</html>
