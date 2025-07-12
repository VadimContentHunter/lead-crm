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

<script type="module">
import {
    NotificationManager
} from '/assets/js/NotificationManager.js';

const notifier = new NotificationManager({
    containerSelector: '.notification-container',
    maxVisible: 2,
    timeout: 2000,
    timeOpacity: 2000
});

// Пример генерации уведомлений
// notifier.add('Уведомление 1', 'info');
// notifier.add('Уведомление 2', 'success');
// notifier.add('Уведомление 3', 'danger');
// notifier.add('Уведомление 4', 'info');
// notifier.add('Уведомление 5', 'success'); // Появится, когда освободится место
// notifier.add('Уведомление 6', 'danger');
</script>
