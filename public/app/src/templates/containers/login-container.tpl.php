<?php

$message = $params['message'] ?? '';
?>
<section class="login-container">
    <form class="base-form" method="post" action="/login">

            <div class="form-message">
                Тестовое сообщение. Ошибка ввели неверные логин и пароль
            </div>

        <div class="form-group">
            <label for="login-username">Логин</label>
            <input type="text" id="login-username" name="username" required>
        </div>

        <div class="form-group">
            <label for="login-password">Пароль</label>
            <input type="password" id="login-password" name="password" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="form-button submit">Войти</button>
        </div>
    </form>
</section>
