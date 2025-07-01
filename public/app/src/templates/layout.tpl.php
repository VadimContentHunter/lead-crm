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
    <!-- Панель навигации -->
    <header class="navbar">
        <div class="user-info">
            Имя пользователя
        </div>
        <div class="actions">
            <button>Настройки</button>
            <button>Выход</button>
        </div>
    </header>

    <!-- Основная страница -->
    <div class="page-container">
        <div class="content-area">
            <!-- Меню внутри контента -->
            <aside class="content-menu">
                <button>Добавить пользователя</button>
                <button>Создать лид</button>
            </aside>

            <!-- Основной контент -->
            <main class="main-content">
                <?= $content ?? '' ?>

                <footer class="footer">
                    &copy; <?= date('Y') ?> CRM Обменка
                </footer>
            </main>
        </div>
    </div>
</body>


</html>
