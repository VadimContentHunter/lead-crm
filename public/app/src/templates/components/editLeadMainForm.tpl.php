<?php

    $sourcesTitle = $sourcesTitle ?? [];
    $statusesTitle = $statusesTitle ?? [];
    $managersLogin = $managersLogin ?? [];
    $selectedData = $selectedData ?? [];
    $fullName = $fullName ?? '';
    $contact = $contact ?? '';
    $address = $address ?? '';
    $leadId = $leadId ?? '';
?>

<section class="component component--medium">
    <h2>Общие сведения лида</h2>
    <form class="base-form edit-lead-form" lead-form-id>
        <input type="text" name="leadId" value="<?= $leadId ?>" hidden>
        <div class="form-messages-container">
            <div class="form-message">
                <p>Введите данные, что бы создать лида.</p>
            </div>
        </div>
        <form class="base-form">
            <div class="form-group">
                <label>Полное имя *</label>
                <input type="text" name="fullName" value="<?= $fullName ?>">
            </div>

            <div class="form-group">
                <label>Контакты *</label>
                <input type="text" name="contact" value="<?= $contact ?>">
            </div>

            <div class="form-group">
                <label>Адрес</label>
                <input type="text" name="address" value="<?= $address ?>">
            </div>

            <div class="form-group">
                <label>Выбор источника</label>
                <select name="sourceId">
                    <option value="">Выберите...</option>
                    <?php foreach ($sourcesTitle as $source) : ?>
                    <option <?= (isset($selectedData['sourceId'])
                                && (int)$selectedData['sourceId'] === (int)($source['id'] ?? 0)
                                ) ? 'selected' : ''
                            ?>
                        value="<?= htmlspecialchars($source['id'] ?? '0') ?>">
                        <?= htmlspecialchars($source['title'] ?? 'error') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="form-group">
                <label>Выбор статусов</label>
                <select name="statusId">
                    <option value="">Выберите...</option>
                    <?php foreach ($statusesTitle as $status) : ?>
                    <option <?= (isset($selectedData['statusId'])
                                && (int)$selectedData['statusId'] === (int)($status['id'] ?? 0)
                                ) ? 'selected' : ''
                            ?>
                        value="<?= htmlspecialchars($status['id'] ?? '0') ?>">
                        <?= htmlspecialchars($status['title'] ?? 'error') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Выбор менеджера</label>
                <select name="accountManagerId">
                    <option value="">Выберите...</option>
                    <?php foreach ($managersLogin as $manager) : ?>
                    <option <?= (isset($selectedData['accountManagerId'])
                                && (int)$selectedData['accountManagerId'] === (int)($manager['id'] ?? 0)
                                ) ? 'selected' : ''
                            ?>
                        value="<?= htmlspecialchars($manager['id'] ?? '0') ?>">
                        <?= htmlspecialchars($manager['login'] ?? 'error') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" class="form-button submit">Сохранить</button>
                <button type="reset" class="form-button">Сбросить</button>
            </div>
        </form>
</section>

<script type="module">
    import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
                        
    ComponentFunctions.attachJsonRpcInputTrigger({
        triggerSelector: '.edit-lead-form[lead-form-id] .form-actions .submit',
        containerSelector: '.edit-lead-form[lead-form-id]',
        method: 'lead.edit',
        endpoint: '/api/leads'
    });
//   window.addEventListener('DOMContentLoaded', () => {
    // ComponentFunctions.attachJsonRpcInputTrigger({
    //   triggerSelector: '#edit-lead-form .form-actions .form-button.submit',
    //   containerSelector: '#edit-lead-form',
    //   method: 'lead.add',
    //   endpoint: '/api/leads'
    // });
//   });
</script>
