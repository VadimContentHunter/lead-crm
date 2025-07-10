<?php

    $login = $login ?? '';
    $userId = $userId ?? '';
?>

<section class="component-wrapper-line">

    <section class="component-wrapper">
        <form class="base-form component" id="add-user-form">
            <input type="text" name="userId" value="<?= $userId ?>" hidden>
            <div class="form-messages-container">
            </div>

            <div class="form-group">
                <label for="login">Логин</label>
                <input type="text" name="login" value="<?= $login ?>">
            </div>

            <div class="form-group">
                <label for="password">Новый Пароль</label>
                <input type="password" name="password">
            </div>

            <div class="form-group">
                <label for="password-input">Новый Повторите пароль</label>
                <input type="password" name="password_confirm">
            </div>

            <div class="form-actions">
                <button type="button" class="form-button submit">Редактировать</button>
                <button type="reset" class="form-button">Сбросить</button>
            </div>
        </form>
    </section>
</section>

<script type="module">
  import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
  
//   window.addEventListener('DOMContentLoaded', () => {
    ComponentFunctions.attachJsonRpcInputTrigger({
      triggerSelector: '#add-user-form .form-actions .form-button.submit',
      containerSelector: '#add-user-form',
      method: 'user.edit',
      endpoint: '/api/users'
    });
//   });
</script>
