<?php

?>

<section class="component-wrapper-line">

    <section class="component-wrapper">
        <form class="base-form component" id="add-status-form">
            <div class="form-messages-container">
                <div class="form-message">
                    <p>Введите название статуса.</p>
                </div>
            </div>

            <div class="form-group">
                <label for="title">Название статуса</label>
                <input type="text" name="title" placeholder="Введите название">
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

  ComponentFunctions.attachJsonRpcInputTrigger({
      triggerSelector: '#add-status-form .form-actions .form-button.submit',
      containerSelector: '#add-status-form',
      method: 'status.add',
      endpoint: '/api/statuses'
  });
</script>
