<?php

// Гарантируем безопасность переменной $components
if (!isset($components) || !is_array($components)) {
    $components = [];
} else {
    // Очищаем всё, что не является строкой
    $components = array_filter($components, 'is_string');

    // Переиндексация, чтобы не было пропусков в ключах
    $components = array_values($components);
}
?>

<div class="content-container">
    <!-- Меню внутри контента -->
    <aside class="header-content">
        <div class="icon-wrapper icon-notification" id="notification">
            <i class="fa-solid fa-bell"></i>
        </div>
        <section class="notification-container">
            <div class="notify notify-info">
                <span class="text">Это просто текстовое уведомление</span>
                <button class="notify-close" type="button">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="notify notify-success">
                <span class="text">Действие выполнено успешно</span>
                <button class="notify-close" type="button">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="notify notify-danger">
                <span class="text">Произошла ошибка при выполнении</span>
                <button class="notify-close" type="button">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

        </section>
    </aside>

    <!-- Основной контент -->
    <main class="main-content content-area">
        <?= implode("\n", $components) ?>
    </main>

    <footer class="footer-content">
        &copy; <?= date('Y') ?> CRM Обменка
    </footer>
</div>