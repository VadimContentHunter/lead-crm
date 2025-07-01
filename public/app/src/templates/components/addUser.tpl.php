<?php

?>

<section class="component-wrapper-line">

    <section class="component component--medium">
        <form class="base-form" id="add-user-form">
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
                <label for="text-input">Логин</label>
                <input type="text" name="text">
            </div>

            <div class="form-group">
                <label for="password-input">Пароль</label>
                <input type="password" name="password">
            </div>

            <div class="form-group">
                <label for="password-input">Повторите пароль</label>
                <input type="password" name="password">
            </div>

            <div class="form-actions">
                <button class="form-button submit">Добавить</button>
                <button type="reset" class="form-button">Сбросить</button>
            </div>
        </form>
    </section>
</section>

<script type="module">
  import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';

  window.addEventListener('DOMContentLoaded', () => {
    ComponentFunctions.attachJsonRpcInputTrigger({
      triggerSelector: '#add-user-form .form-actions .form-button.submit',
      containerSelector: '#add-user-form',
      method: 'user.add'
    });
  });
</script>
