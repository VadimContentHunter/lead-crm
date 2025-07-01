<?php

$message = $params['message'] ?? '';
?>
<section class="login-container">
    <form class="base-form" method="post" action="/login">
        <div class="form-messages-container">
            <div class="form-message error">
                <p>Ошибка: неверный логин или пароль.</p>
            </div>
            <div class="form-message success">
                <p>Вы успешно вошли!</p>
            </div>
            <div class="form-message">
                <p>Введите свои учётные данные.</p>
            </div>
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
            <button class="form-button submit">Войти</button>
        </div>
    </form>
</section>
