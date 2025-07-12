<?php

    $login = $login ?? '';
    $role = $role ?? '---';
    $space = $space ?? '---';
    $menuItems = $menuItems ?? [];
?>

<!-- Основное меню -->
<aside class="main-menu">
    <!-- Панель навигации -->
    <header class="header">
        <div class="icon-main">
            <i class="fa-solid fa-chart-simple"></i>
            <!-- <i class="fas fa-leaf"></i> -->
        </div>
        <p>CRM</p>
    </header>

    <section class="user-info">
        <p>Логин: <span><?= htmlspecialchars($login) ?></span></p>
        <div class="user-data">
            <p>Роль: <span><?= htmlspecialchars($role) ?></span></p>
            <p>Пространство: <span><?= htmlspecialchars($space) ?></span></p>
        </div>
    </section>

    <nav class="list-main-menu top-menu">
        <?php foreach ($menuItems as $item) : ?>
        <a href="<?= htmlspecialchars($item['href']) ?>" class="item-main-menu">
            <div class="icon-wrapper">
                <i class="<?= htmlspecialchars($item['icon'] ?? 'fa-solid fa-house') ?>"></i>
            </div>
            <p><?= htmlspecialchars($item['name']) ?></p>
        </a>
        <?php endforeach; ?>
    </nav>

    <nav class="list-main-menu bottom-menu">
        <a href="/logout" class="item-main-menu">
            <div class="icon-wrapper">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>
            <p>Выход</p>
        </a>
    </nav>
</aside>
