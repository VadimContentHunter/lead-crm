<?php

    $login = $login ?? '';
    $role = $role ?? '---';
    $space = $space ?? '---';
?>

<!-- Основное меню -->
<aside class="main-menu">
    <!-- Панель навигации -->
    <header class="header">
        <div class="icon-main">
            <i class="fas fa-leaf"></i>
        </div>
        <p>CRM</p>
    </header>

    <section class="user-info">
        <div class="user-data">
            <p>Логин: <span><?= $login ?></span></p>
            <p>Роль: <span><?= $role ?></span></p>
            <p>Пространство: <span><?= $space ?></span></p>
        </div>
        
    </section>

    <nav class="list-main-menu top-menu">
        <a href="/test" class="item-main-menu">
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Главная</p>
        </a>

        <a href="/page/user-add" class="item-main-menu">
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Добавить пользователя</p>
        </a>
        <a href="/page/user-all" class="item-main-menu">
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Все пользователя</p>
        </a>
        <!-- <div class="item-main-menu"
            data-rpc-method="user.show.add_page"
            data-rpc-endpoint="/api/users"
            id="add-user-button">
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Добавить пользователя</p>
        </div> -->

        <a href="/page/status-add" class="item-main-menu">
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Добавить статус</p>
        </a>

        <a href="/page/status-all" class="item-main-menu">
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Статусы</p>
        </a>
        
        <a href="/page/source-add" class="item-main-menu">
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Добавить источник</p>
        </a>

        <a href="/page/source-all" class="item-main-menu">
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Источники</p>
        </a>

        <a href="/page/lead-add" class="item-main-menu">
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Добавить лида</p>
        </a>

        <a href="/page/lead-all" class="item-main-menu">
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Все лиды P2P</p>
        </a>

        <a href="/page/lead-all" class="item-main-menu">
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Все лиды (Инвестка)</p>
        </a>
    </nav>

    <nav class="list-main-menu bottom-menu">
        <div class="item-main-menu item-logout">
            <div class="icon-wrapper">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>
            <p>Выход</p>
        </div>
    </nav>
</aside>

<!-- Инициализация обработчика кликов -->
<!-- <script type="module">
    import { createContentUpdateHandler } from '/assets/js/CreateContentUpdateHandler.js';
    import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';

    const endpointAttr = 'data-rpc-endpoint';
    // window.addEventListener('DOMContentLoaded', () => {
        ComponentFunctions.attachJsonRpcTriggerFromAttributes({
            triggerSelector: '#all-user-button[data-rpc-method]',
            endpointAttr,
            onContentUpdate: createContentUpdateHandler()
        });

        ComponentFunctions.attachJsonRpcTriggerFromAttributes({
            triggerSelector: '#statuses-sources-button[data-rpc-method]',
            endpointAttr,
            onContentUpdate: createContentUpdateHandler()
        });
        
    // });
</script> -->
