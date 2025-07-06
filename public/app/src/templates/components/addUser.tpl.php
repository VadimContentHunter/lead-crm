<?php

use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\Security\_entities\AccessSpace;

$roles = $roles ?? [];
$spaces = $spaces ?? [];
?>

<section class="component-wrapper-line">

    <section class="component component--medium">
        <form class="base-form" id="add-user-form">
            <div class="form-messages-container">
                <div class="form-message">
                    <p>Введите свои учётные данные.</p>
                </div>
            </div>

            <div class="form-group">
                <label for="login">Логин</label>
                <input type="text" name="login">
            </div>

            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" name="password">
            </div>

            <div class="form-group">
                <label for="password-input">Повторите пароль</label>
                <input type="password" name="password_confirm">
            </div>

            <div class="form-group">
                <label>Выбор роли</label>
                <select name="role_id">
                    <option value="">Выберите...</option>
                    <?php foreach ($roles as $role) {
                        if ($role instanceof AccessRole) {
                            echo '<option value="' . ($role->id ?? '0') . '">' . ($role->name ?? '-роль-') . '</option>';
                        }
                    }?>
                </select>
            </div>

            <div class="form-group">
                <label>Выбор пространства</label>
                <select name="space_id">
                    <?php foreach ($spaces as $space) {
                        if ($space instanceof AccessSpace) {
                            echo '<option value="' . ($space->id ?? '0') . '">' . ($space->name  ?? '-пространство-') . '</option>';
                        }
                    }?>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" class="form-button submit">Добавить</button>
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
      method: 'user.add',
      endpoint: '/api/users'
    });
//   });
</script>
