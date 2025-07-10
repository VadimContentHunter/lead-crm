<?php

?>

<section class="component-wrapper-line">

    <section class="component-wrapper">
        <form class="base-form component" id="add-source-form">
            <div class="form-messages-container">
                <div class="form-message">
                    <p>Введите название источника.</p>
                </div>
            </div>

            <div class="form-group">
                <label for="title">Название <i style="color:rgb(0, 100, 146)">источника</i></label>
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
      triggerSelector: '#add-source-form .form-actions .form-button.submit',
      containerSelector: '#add-source-form',
      method: 'source.add',
      endpoint: '/api/sources'
  });
</script>
