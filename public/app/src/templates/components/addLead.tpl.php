<?php

    $sourcesTitle = $sourcesTitle ?? [];
    $statusesTitle = $statusesTitle ?? [];
    $managersLogin = $managersLogin ?? [];
?>

<section class="component-wrapper-line">
    <section class="component-wrapper"  id="add-lead-form">
            <div class="form-messages-container">
                <div class="form-message">
                    <p>Введите данные, что бы создать лида.</p>
                </div>
            </div>
            <form class="base-form component">
                <div class="form-group">
                    <label>Полное имя *</label>
                    <input type="text" name="fullName">
                </div>

                <div class="form-group">
                    <label>Контакты *</label>
                    <input type="text" name="contact">
                </div>

                <div class="form-group">
                    <label>Адрес</label>
                    <input type="text" name="address">
                </div>

                <div class="form-group">
                    <label>Выбор источника</label>
                    <select name="sourceId">
                        <option value="">Выберите...</option>
                        <?php foreach ($sourcesTitle as $source) : ?>
                            <option value="<?= htmlspecialchars($source['id'] ?? '0') ?>">
                                <?= htmlspecialchars($source['title'] ?? 'error') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="form-group">
                    <label>Выбор статусов</label>
                    <select name="statusId">
                        <option value="">Выберите...</option>
                        <?php foreach ($statusesTitle as $source) : ?>
                            <option value="<?= htmlspecialchars($source['id'] ?? '0') ?>">
                                <?= htmlspecialchars($source['title'] ?? 'error') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Выбор менеджера</label>
                    <select name="accountManagerId">
                        <option value="">Выберите...</option>
                        <?php foreach ($managersLogin as $manager) : ?>
                            <option value="<?= htmlspecialchars($manager['id'] ?? '0') ?>">
                                <?= htmlspecialchars($manager['login'] ?? 'error') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button class="form-button submit">Сохранить</button>
                    <button type="reset" class="form-button">Сбросить</button>
                </div>
            </form>
    </section>
</section>

<script type="module">
  import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
  
//   window.addEventListener('DOMContentLoaded', () => {
    ComponentFunctions.attachJsonRpcInputTrigger({
      triggerSelector: '#add-lead-form .form-actions .form-button.submit',
      containerSelector: '#add-lead-form',
      method: 'lead.add',
      endpoint: '/api/leads'
    });
//   });
</script>
