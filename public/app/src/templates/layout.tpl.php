<?php

$title = $title ?? 'Без названия';
$head = $head ?? '';
$content = $content ?? '';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <?= $head ?>
</head>
<body>
    <!-- Основная страница -->
    <div class="page-container">

        <!-- Основное меню -->
        <aside class="main-menu">
            <!-- Панель навигации -->
            <header class="header">
                <div class="icon-wrapper icon-main">
                    <i class="fas fa-leaf"></i>
                </div>
                <p>CRM</p>
            </header>
            <nav class="top-menu">

            </nav>
            <nav class="bottom-menu">

            </nav>
        </aside>


        <div class="content-container">
            <!-- Меню внутри контента -->
            <aside class="header-content">
                <nav class="menu-content">

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
