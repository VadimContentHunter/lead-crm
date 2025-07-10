<?php

$message = $params['message'] ?? '';
?>
<section class="login-container">
    <form class="base-form component" id="login-form">
        <div class="form-messages-container">
        </div>

        <div class="form-group">
            <label for="login-username">Логин</label>
            <input type="text" name="login" required>
        </div>

        <div class="form-group">
            <label for="login-password">Пароль</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-actions">
            <button class="form-button submit">Войти</button>
        </div>
    </form>
</section>

<script type="module">
  import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
  
//   window.addEventListener('DOMContentLoaded', () => {
    ComponentFunctions.attachJsonRpcInputTrigger({
      triggerSelector: '#login-form .form-actions .form-button.submit',
      containerSelector: '#login-form',
      method: 'auth.login',
      endpoint: '/api/login'
    });
//   });
</script>