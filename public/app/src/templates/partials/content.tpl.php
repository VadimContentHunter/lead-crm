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
        <nav class="menu-content main-wrapper-horizontal">
            <button type="button" class="default-btn">Сохранить</button>
            <button type="button" class="default-btn">Удалить</button>
        </nav>
    </aside>

    <!-- Основной контент -->
    <main class="main-content content-area">
        <?= implode("\n", $components) ?>
    </main>

    <footer class="footer-content">
            &copy; <?= date('Y') ?> CRM Обменка
    </footer>
</div>
