<?php

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
            <p>Логин: <span>admin</span></p>
            <p>Данные 1: <span>Тест</span></p>
        </div>
        
    </section>

    <nav class="list-main-menu top-menu">
        <div class="item-main-menu" data-rpc-method="page.update" >
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Главная</p>
        </div>

        <div class="item-main-menu"
            data-rpc-method="user.show.add_page"
            data-rpc-endpoint="/api/users"
            id="add-user-button">
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Добавить пользователя</p>
        </div>

        <div class="item-main-menu"
            data-rpc-method="user.show.all_page"
            data-rpc-endpoint="/api/users"
            id="all-user-button">
            <div class="icon-wrapper">
                <i class="fa-solid fa-house"></i>
            </div>
            <p>Все пользователя</p>
        </div>
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
<script type="module">
    import { createContentUpdateHandler } from '/assets/js/CreateContentUpdateHandler.js';
    import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';

    const endpointAttr = 'data-rpc-endpoint';
    // window.addEventListener('DOMContentLoaded', () => {
        ComponentFunctions.attachJsonRpcTriggerFromAttributes({
            triggerSelector: '#add-user-button[data-rpc-method]',
            endpointAttr,
            onContentUpdate: createContentUpdateHandler()
        });

        ComponentFunctions.attachJsonRpcTriggerFromAttributes({
            triggerSelector: '#all-user-button[data-rpc-method]',
            endpointAttr,
            onContentUpdate: createContentUpdateHandler()
        });
    // });
</script>
